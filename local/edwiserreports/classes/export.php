<?php
// This file is part of Moodle - http://moodle.org/
//
// Moodle is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.
//
// Moodle is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with Moodle.  If not, see <http://www.gnu.org/licenses/>.
/**
 * Plugin administration pages are defined here.
 *
 * @package     local_edwiserreports
 * @category    admin
 * @copyright   2019 wisdmlabs <support@wisdmlabs.com>
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace local_edwiserreports;

defined('MOODLE_INTERNAL') || die();

require_once($CFG->libdir."/csvlib.class.php");
require_once($CFG->libdir."/excellib.class.php");
require_once($CFG->libdir."/pdflib.php");
require_once($CFG->dirroot."/local/edwiserreports/classes/utility.php");
require_once($CFG->dirroot."/local/edwiserreports/lib.php");
require_once($CFG->dirroot."/local/edwiserreports/locallib.php");
require_once($CFG->dirroot."/local/edwiserreports/classes/output/renderable.php");

use csv_export_writer;
use moodle_exception;
use core_user;
use context_course;
use MoodleExcelWorkbook;
use pdf;
use html_table;
use html_writer;
use html_table_row;
use html_table_cell;
use file_storage;
use stdClass;
use moodle_url;
use context_system;
use context_user;
use core_course_category;

class export {
    /**
     * Export data in this format
     */
    public $format = null;

    /**
     * Region to download reports
     * This may be block or report
     */
    public $region = null;

    /**
     * Action to get data for specific block
     */
    public $blockname = null;

    /**
     * Constructor to create export object
     * @param $format type os export object
     */
    public function __construct($format, $region, $blockname) {
        $this->format = $format;
        $this->region = $region;
        $this->blockname = $blockname;
    }

    /**
     * Export data
     * @param $filenme file name to export data
     * @param $data data to be export
     * @return Return status after export the data
     */
    public function data_export($filename, $data) {
        switch($this->format) {
            case "csv":
                $this->data_export_csv($filename, $data);
                break;
            case "excel":
                $this->data_export_excel($filename, $data);
                break;
            case "pdf":
                $this->data_export_pdf($filename, $data);
                break;
            case "email":
                $this->data_export_email($filename, $data);
                break;
            case "emailscheduled":
                $this->data_export_emailscheduled($filename);
                break;
        }
    }

    /**
     * Export data in CSV format
     * @param $filenme file name to export data
     * @param $data data to be export
     * @return Return status after export the data
     */
    public function data_export_csv($filename, $data) {
        csv_export_writer::download_array($filename, $data);
    }

    /**
     * Prepare output for export data
     */
    public function prepare_output($data, $message, $status) {
        $res = new stdClass();
        $res->status = $status;
        $res->message = $message;
        $res->data = $data;

        // Print Output.
        echo json_encode($res);
    }

    /**
     * Export data in Excel format
     * @param $filenme file name to export data
     * @param $data data to be export
     * @return Return status after export the data
     */
    public function data_export_excel($filename, $data) {
        // Creating a workbook.
        $workbook = new MoodleExcelWorkbook("-");

        // Adding the worksheet.
        $myxls = $workbook->add_worksheet($this->region . "_" . $this->blockname);

        foreach ($data as $rownum => $row) {
            foreach ($row as $colnum => $val) {
                $myxls->write_string($rownum, $colnum, $val);
            }
        }

        // Sending HTTP headers.
        $workbook->send($filename);
        // Close the workbook.
        $workbook->close();
    }

    /**
     * Export data in Pdf format
     * @param $filenme file name to export data
     * @param $data data to be export
     * @return Return status after export the data
     */
    public function data_export_pdf($filename, $data) {
        $filename .= '.pdf';
        $html = $this->generate_pdf_file($data);

        $res = new stdClass();
        $res->error = false;
        $res->data = array(
            "filename" => $filename,
            "html" => $html
        );
        echo json_encode($res);
        die;
    }

    /**
     * Genereate csv file to export
     * @param  [string] $filename Filename
     * @param  [array] $data Data to render
     * @return [string] File path
     */
    public function generate_csv_file($filename, $data) {
        global $USER, $CFG;

        $context = context_user::instance($USER->id);
        $fs = get_file_storage();

        // Prepare file record object.
        $fileinfo = array(
            'contextid' => $context->id, // ID of context
            'component' => 'local_edwiserreports',     // usually = table name
            'filearea' => 'downloadreport',     // usually = table name
            'itemid' => 0,               // usually = ID of row in table
            'filepath' => '/',           // any path beginning and ending in /
            'filename' => $filename); // any filename.

        // Create csv data.
        $csvdata = csv_export_writer::print_array($data, 'comma', '"', true);

        // Get file if already exist.
        $file = $fs->get_file(
            $fileinfo['contextid'],
            $fileinfo['component'],
            $fileinfo['filearea'],
            $fileinfo['itemid'],
            $fileinfo['filepath'],
            $fileinfo['filename']
        );

        // Delete it if it exists.
        if ($file) {
            $file->delete();
        }

        // Create file containing text 'hello world'.
        $file = $fs->create_file_from_string($fileinfo, $csvdata);
        $fileurl = moodle_url::make_pluginfile_url(
            $fileinfo['contextid'],
            $fileinfo['component'],
            $fileinfo['filearea'],
            $fileinfo['itemid'],
            $fileinfo['filepath'],
            $fileinfo['filename'],
            false
        );

        // Copy content to temporary file.
        $filepath = $CFG->tempdir . '/' . $filename;
        $file->copy_content_to($filepath);

        // Delete file when content has been copied.
        if ($file) {
            $file->delete();
        }

        return $filepath;
    }

    /**
     * Export data email to user
     * @param $filenme file name to export data
     * @param $data data to be export
     * @return Return status after export the data
     */
    public function data_export_email($filename, $data) {
        global $CFG, $USER;
        $recuser = $USER;
        $senduser = core_user::get_noreply_user();

        // Generate csv file.
        $filename .= ".csv";
        $filepath = $this->generate_csv_file($filename, $data);

        //@codingStandardsIgnoreStart
        // Generate PDF file
        // $filename .= '.pdf';
        // $filepath = $this->generate_pdf_file($filename, $data, "F");
        //@codeingStandardsIgnoreEnd

        // Get email data from submited form
        $emailids = trim(optional_param("esrrecepient", false, PARAM_TEXT));
        $subject = trim(optional_param("esrsubject", false, PARAM_TEXT));

        // Optional parameter causing issue because this is an array
        $contenttext = optional_param('esrmessage', '', PARAM_TEXT);

        // If subject is not set the get default subject
        if (!$subject && $subject == '') {
            $subject = get_string($this->blockname . "exportheader", "local_edwiserreports");
        }

        // Send emails foreach email ids.
        if ($emailids && $emailids !== '') {
            // process in background and dont show message in console.
            ob_start();
            $emailids = explode(";", $emailids);
            foreach($emailids as $emailcommaids) {
                foreach(explode(",", $emailcommaids) as $emailid) {
                    // trim email id if white spaces are added
                    $recuser->email = trim($emailid);

                    // Send email to user
                    email_to_user(
                        $recuser,
                        $senduser,
                        $subject,
                        '',
                        $contenttext,
                        $filepath,
                        $filename
                    );
                }
            }
            ob_end_clean();

            // If failed then return error
            $res = new stdClass();
            $res->error = false;
            $res->errormsg = get_string('emailsent', 'local_edwiserreports');
            echo json_encode($res);
        } else {
            // If failed then return error
            $res = new stdClass();
            $res->error = true;
            $res->errormsg = get_string('emailnotsent', 'local_edwiserreports');
            echo json_encode($res);
        }

        // Remove file after email sending process
        unlink($filepath);
    }

    /**
     * Save data scheduled email for users
     * @param $filenme file name to export data
     * @return Return status after save the scheduled email data
     */
    public function data_export_emailscheduled($filename) {
        global $CFG, $DB;
        $response = new stdClass();
        $response->error = false;

        $data = new stdClass();
        $data->blockname = $this->blockname;
        $data->component = $this->region;

        $table = "edwreports_schedemails";
        $sql = "SELECT id, emaildata FROM {edwreports_schedemails}
            WHERE blockname = :blockname
            AND component = :component";
        if ($rec = $DB->get_record_sql($sql, (array)$data)) {
            $data->id = $rec->id;
            list($id, $data->emaildata) = $this->get_email_data($rec->emaildata);
            $DB->update_record($table, $data);
        } else {
            list($id, $data->emaildata) = $this->get_email_data();
            $DB->insert_record($table, $data);
        }

        $args = array(
            "id" => optional_param("esrid", null, PARAM_INT),
            "blockname" => $this->blockname,
            "region" => $this->region,
            "href" => $CFG->wwwroot . $_SERVER["REQUEST_URI"]
        );

        // Return data in json format
        echo json_encode($response);
    }

    /**
     * Get scheduled email data
     * @return [string] email data encoded object
     */
    private function get_email_data($emaildata = false) {
        global $DB;
        // Generate default email information array
        $emailinfo = array(
            'esrname' => required_param("esrname", PARAM_TEXT),
            'esremailenable' => optional_param("esremailenable", false, PARAM_TEXT),
            'esrrecepient' => required_param("esrrecepient", PARAM_TEXT),
            'esrsubject' => optional_param("esrsubject", '', PARAM_TEXT),
            'esrmessage' => optional_param("esrmessage", '', PARAM_TEXT),
            'esrduration' => optional_param("esrduration", 0, PARAM_TEXT),
            'esrtime' => optional_param("esrtime", 0, PARAM_TEXT),
            'esrlastrun' => false,
            'esrnextrun' => false,
            'reportparams' => array(
                'filter' => optional_param("filter", false, PARAM_TEXT),
                'blockname' => $this->blockname,
                'region' => optional_param("region", false, PARAM_TEXT)
            )
        );

        // Calculate Next Run
        list($fequency, $nextrun) = local_edwiserreports_get_email_schedule_next_run($emailinfo["esrduration"], $emailinfo["esrtime"]);

        $emailinfo["esrnextrun"] = $nextrun;
        $emailinfo["esrfrequency"] = $fequency;

        // Get previous data and update
        if (!$emaildata = json_decode($emaildata)) {
            $emaildata = array($emailinfo);
        } else if (is_array($emaildata)){
            $esrid = optional_param("esrid", false, PARAM_INT);
            if ($esrid < 0) {
                $emaildata[] = $emailinfo;
            } else {
                $emaildata[$esrid] = $emailinfo;
            }
        }

        // Return array if of data and encoded email data
        return array((count($emaildata) - 1), json_encode($emaildata));
    }

    /**
     * Generate PDF file to export
     * @param [string] $filename File Name
     * @param [array] $data Data to export
     * @param [string] $destination location to create file
     * @return [string] File Path
     */
    public function generate_pdf_file($data) {
        global $CFG;
        // $pdf = new pdf();

        // $pdf->setPrintHeader(false);
        // $pdf->setPrintFooter(false);
        // $pdf->SetAutoPageBreak(true, 72);

        // $pdf->AddPage();

        // Generate HTML to export
        ob_start();
        $html = $this->get_html_for_pdf2($data);
        ob_clean();


        //  Create proper HTML ro export in PDF
        // $pdf->writeHTML($html);

        // $pdf->Output($filepath, $dest);

        return $html;
    }

    /**
     * Get HTML Content to export
     * @param  [array] $data Array of exportable Data
     * @return [string] HTML String
     */
    public function get_html_for_pdf($data) {
        $table = new html_table();
        $table->align = array("left", "left", "center", "center", "center");
        $table->attributes = array(
            "style" => "width: 100%; font-size:10px;"
        );

        // Generate HTML to export.
        $html = html_writer::tag(
            "h1",
            get_string($this->blockname . "exportheader", "local_edwiserreports"),
            array(
                "style" => "width:100%;text-align:center"
            )
        );

        $html .= html_writer::tag("p",
            get_string($this->blockname . "exporthelp", "local_edwiserreports"),
            array(
                "style" => "text-indent: 50px"
            )
        );

        foreach ($data as $key => $val) {
            if ($key == 0) {
                $table->head = $val;
            } else {
                $table->data[] = $val;
            }
        }

        $html .= html_writer::table($table);
        $html = str_replace("\n","", $html);
        return $html;
    }

    /**
     * Get HTML Content to export
     * @param  [array] $data Array of exportable Data
     * @return [string] HTML String
     */
    public function get_html_for_pdf2($data) {
        // Generate HTML to export
        $html = html_writer::tag("h1",
            get_string($this->blockname . "exportheader", "local_edwiserreports"),
            array(
                "style" => "width:100%;text-align:center"
            )
        );

        $html .= html_writer::tag("p",
            get_string($this->blockname . "exporthelp", "local_edwiserreports"),
            array(
                "style" => "text-indent: 50px"
            )
        );

        $html .= "<table style='font-size: 10px; width: 50px; display: block;'>";

        foreach ($data as $key => $val) {
            $html .= "<tr>";
            $width = 0;
            if ($key == 0) {
                foreach ($val as $v) {
                    $cols = count($val);
                    $width = 100 / $cols;
                    $html .= "<th style='background-color: #ddd; width: " . $width . "%; display: block; word-break: break-word;'>".$v."</th>";
                }
            } else {
                foreach ($val as $v) {
                    $html .= "<td style='background-color: #ddd; " . $width . "%; display: block; word-break: break-word;'>".$v."</td>";
                }
            }
            $html .= "</tr>";
        }

        $html .= '</table>';
        $html = str_replace("\n","", $html);
        return $html;
    }

    /**
     * Get exportable data to export
     * @param $filenme file name to export data
     * @param $data data to be export
     * @return Return status after export the data
     */
    public function get_exportable_data($filter) {
        $export = null;

        switch ($this->region) {
            case "block":
                $export = $this->exportable_data_block($this->blockname, $filter);
                break;
            case "report":
                $export = $this->exportable_data_report($this->blockname, $filter);
                break;
        }

        return $export;
    }

    /**
     * Get exportable data for dashboard block
     * @param [string] $blockname Block to get exportable data
     * @param [string] $filter Filter to get data
     * @return [array] Array of exportable data
     */
    private function exportable_data_block($blockname, $filter) {
        global $CFG;

        // Check if class file exist
        $classname = $blockname;
        $filepath = $CFG->dirroot . '/local/edwiserreports/classes/blocks/' . $classname . '.php';
        if (!file_exists($filepath)) {
            debugging('Class file dosn\'t exist ' . $classname);
        }
        require_once($filepath);

        $classname = '\\local_edwiserreports\\' . $classname;
        $blockbase = new $classname();

        return $blockbase->get_exportable_data_block($filter);
    }

    /**
     * Get exportable data for individual page
     * @param [string] $blockname Block to get exportable data
     * @param [string] $filter Filter to get data
     * @return [array] Array of exportable data
     */
    private function exportable_data_report($blockname, $filter) {
        global $CFG;
        $export = null;

        switch ($blockname) {
            case "activeusersblock":
                require_once($CFG->dirroot . '/local/edwiserreports/classes/blocks/activeusersblock.php');
                $export = activeusersblock::get_exportable_data_report($filter);
                break;
            case "courseprogressblock":
                $export = courseprogressblock::get_exportable_data_report($filter);
                break;
            case "courseengageblock":
                $export = courseengageblock::get_exportable_data_report();
                break;
            case "certificatesblock":
                $export = certificatesblock::get_exportable_data_report($filter);
                break;
            case "f2fsession":
                $export = f2fsession_block::get_exportable_data_report($filter);
                break;
            case "lpstats":
                $export = lpstats_block::get_exportable_data_report($filter);
                break;
            case "completionblock":
                $export = completionblock::get_exportable_data_report($filter);
                break;
            case "courseanalytics":
                $export = courseanalytics_block::get_exportable_data_report($filter);
                break;
        }
        return $export;
    }

    /**
     * Set CSV header to download files in csv format
     */
    public function set_csv_header($filename) {
        header("Content-type: text/csv");
        header("Content-Disposition: attachment; filename=" . $filename);
        header("Pragma: no-cache");
        header("Expires: 0");
    }

    /**
     * Get Lp detailed reports
     * @param  [type] $lpid [description]
     * @param  [type] $xls  [description]
     * @return [type]       [description]
     */
    public function get_lpdetailed_report_excel($lpid, &$workbook, $cohortid = false) {
        global $CFG, $DB;

        require_once($CFG->dirroot."/local/edwiserreports/classes/blocks/lpstats_block.php");

        // Get learning program
        $table = 'wdm_learning_program';
        $lp = $DB->get_record($table, array('id' => $lpid));

        // If no learning program found
        if (!$lp) {
            return false;
        }
        // Adding the worksheet
        $xls = $workbook->add_worksheet($lp->name);

        // Get Lp reports
        $lpreports = \local_edwiserreports\lpstats_block::get_lpstats_usersdata($lpid, $cohortid);

        // Prepare report header
        $header = \local_edwiserreports\lpstats_block::get_header_report();
        $courseids = json_decode($lp->courses);
        foreach ($lpreports->courses as $course) {
            $header[] = $course->shortname;
        }

        // Add additional headers
        $header = array_merge($header, array(
            get_string('avgprogress', 'local_edwiserreports'),
            get_string('completedactivity', 'local_edwiserreports'),
        ));

        // Add custom fields
        $this->inseart_custom_filed_header($header);

        // Render reporting header
        $colnum = 0;
        foreach (array_values($header) as $colnum => $head) {
            $xls->write_string(0, $colnum, $head);
        }

        // Add reports data
        foreach($lpreports->users as $key => $user) {
            $colnum = 0;
            $ckey = $key + 1;
            $xls->write_string($ckey, $colnum++, $user->name);
            $xls->write_string($ckey, $colnum++, $user->email);
            $xls->write_string($ckey, $colnum++, $user->enrolled);
            $xls->write_string($ckey, $colnum++, $user->lastaccess);
            $xls->write_string($ckey, $colnum++, $user->grade);

            // Prepare progress fpor each courses
            foreach ($user->progress as $progress) {
                $xls->write_string($ckey, $colnum++, $progress);
            }

            // Add avg progress
            $xls->write_string($ckey, $colnum++, $user->avgprogress);

            // Add completed activities
            $xls->write_string($ckey, $colnum++, $user->completedactivities);

            // Add custom data
            $customdata = new stdClass();
            $this->inseart_custom_filed_data($customdata, $user->id);
            foreach ($customdata as $data) {
                $xls->write_string($ckey, $colnum++, $data);
            }
        }
    }

    /**
     * Export Learning progra
     * @param  [type] $type      [description]
     * @param  [type] $filters   [description]
     * @param  [type] $startdate [description]
     * @param  [type] $enddate   [description]
     * @return [type]            [description]
     */
    public function export_lpdetailed_report_data($type, $filters, $startdate, $enddate) {
        $filename = 'Lp_Deailed_Reports_';

        $filename .= date('d_m_Y', time()) . '.xls';

        // Get Cohort ID
        $cohortid = optional_param('cohortid', false, PARAM_INT);

        // Creating a workbook
        $workbook = new MoodleExcelWorkbook($filename);

        // For each pages create worksheet
        $filters = explode(',', $filters);
        foreach($filters as $filter) {
            $this->get_lpdetailed_report_excel($filter, $workbook, $cohortid);
        }

        // Sending HTTP headers
        $workbook->send($filename);

        // Close the workbook
        $workbook->close();
    }
    /**
     * Export CSV for custom query report
     * @param  [strinf] $fields              [Selected Fields]
     * @param  [string] $lps                 [Selected learning programs]
     * @param  [string] $courses             [Selected courses]
     * @param  [string] $enrolstartdate      [Selected course enroll start date]
     * @param  [string] $enrolenddate        [Selected course enroll end date]
     * @param  [string] $completionstartdate [Selected course completion start date]
     * @param  [string] $completionenddate   [Selected course completion end date]
     */
    public function export_csv_customquery_report_data($data) {
        global $DB;

        $fields = $data->fields;
        $lps = $data->lps;
        $courses = $data->courses;
        $enrolstartdate = $data->enrolstartdate;
        $enrolenddate = $data->enrolenddate;
        $completionstartdate = $data->completionstartdate;
        $completionenddate = $data->completionenddate;
        $cohortids = $data->cohortids;
        $userids = $data->userids;
        $reportlevel = $data->reportlevel;
        $activitytype = $data->activitytype;

        $fields = explode(',', $fields);
        $fields = $this->get_filter_based_fields($lps, $fields);
        $params = [];

        //if enroldate not selected
        if ($enrolenddate == "") {
            $enrolenddate = time();
        } else {
            $enrolenddate += 24 * 60 * 60 - 1;
        }

        //if completiondate not selected
        $completionsql =  '';
        if ($completionenddate !== "") {
            $completionenddate += 24 * 60 * 60 - 1;
            // $completionsql =  ' AND ec.timecompleted >= :completionstartdate AND ec.timecompleted <= :completionenddate';
        }
        // get selected fields in query format
        list($customFields, $headers) =  $this->create_query_fields($fields);

        $params = array();
        // check courses
        $courses = explode(',', $courses);
        $coursedb = '> 1';
        if (!in_array(0, $courses)) {
            list($coursedb, $inparams) = $DB->get_in_or_equal($courses, SQL_PARAMS_NAMED, 'course', true,true);
            $params = array_merge($params, $inparams);
        }
        // check learning programs
        $lpdb = '';
        $lpjoinquery = '';
        if ($lps !== "") {
            $lpdb = '> 0';
            $lps = explode(',', $lps);
            // get lps in In query
            if (!in_array(0, $lps)) {
                list($lpdb, $inparams) = $DB->get_in_or_equal($lps, SQL_PARAMS_NAMED, 'lps', true, true);
                $params = array_merge($params, $inparams);
            }
            $tablename = 'lp_course_data';
            $isChecked = $this->create_temp_table($tablename, $lpdb, $params);
            $lpjoinquery = 'JOIN {wdm_learning_program_enrol} lpe ON lpe.userid = u.id AND lpe.learningprogramid '.$lpdb.'
                JOIN {wdm_learning_program} lp ON lp.id = lpe.learningprogramid
                JOIN {lp_course_data} lcd ON lcd.courseid = c.id AND lcd.lpid = lp.id';
        }

        // Check if learning hour plugin is available
        $lhdb = '';
        if (local_edwiserreports_has_plugin('report', 'learning_hours')) {
            $lhdb = 'LEFT JOIN {edw_learning_hours} lh ON lh.courseid = c.id
                LEFT JOIN {edw_users_learning_hours} ulh ON ulh.userid = u.id AND ulh.lhid = lh.id';
        }

        // Check Cohorts
        $allusers = false;
        if ($cohortids === "0") {
            $cohorts = \local_edwiserreports\utility::get_cohort_users(array(0));
            $userids = array_column($cohorts['users'], 'id');
        } else if ($cohortids !== "") {
            if ($userids === "0") {
                $cohortids = explode(",", $cohortids);
                $cohorts = \local_edwiserreports\utility::get_cohort_users($cohortids);
                $userids = array_column($cohorts['users'], 'id');
            } else {
                $userids = explode(",", $userids);
            }
        } else {
            if ($userids === "0" || $userids === "") {
                $allusers = true;
            } else {
                $userids = explode(",", $userids);
            }
        }

        $userdb = '';
        if (!$allusers) {
            list($userdb, $uparams) = $DB->get_in_or_equal($userids, SQL_PARAMS_NAMED, 'user', true, true);
            $params = array_merge($params, $uparams);
        }

        // Check for report type
        $activitytypejoin = '';
        $activitytypedb = '';
        if ($reportlevel == 'activities') {
            $activitytype = $DB->get_record("modules", array("id" => $activitytype));

            if (!empty($activitytype)) {
                switch ($activitytype->name) {
                    case 'quiz':
                        $activitytypejoin = 'JOIN {quiz_grades} qg ON qg.userid = u.id
                                             JOIN {quiz} q ON q.id = qg.quiz AND q.course = c.id
                                             JOIN {quiz_attempts} qa ON qa.userid = u.id AND qa.quiz = q.id';
                        break;
                    default:
                        break;
                }
            }
        }

        // Main query to execute the custom query reports
        $sql = 'SELECT (@cnt := @cnt + 1) AS id, '.$customFields.' FROM {user} u
                CROSS JOIN (SELECT @cnt := 0) AS dummy
                JOIN {role_assignments} ra ON ra.userid = u.id
                JOIN {role} r ON r.id = ra.roleid
                JOIN {context} ct ON ct.id = ra.contextid
                JOIN {course} c ON c.id = ct.instanceid '.$lpjoinquery.' ' . $activitytypejoin . '
                JOIN {edwreports_course_progress} ec ON ec.courseid = c.id AND ec.userid = u.id AND c.id '.$coursedb.'
                JOIN {course_categories} ctg ON ctg.id = c.category ' . $lhdb . '
                WHERE u.id '.$userdb.'
                AND ct.contextlevel = '.CONTEXT_COURSE.'
                AND r.archetype = "student"
                AND u.deleted = false
                AND ra.timemodified >= :enrolstartdate AND ra.timemodified <= :enrolenddate'.$completionsql;
        $params['enrolstartdate'] = $enrolstartdate;
        $params['enrolenddate'] = $enrolenddate;
        $params['completionstartdate'] = $completionstartdate;
        $params['completionenddate'] = $completionenddate;
        $records = $DB->get_records_sql($sql, $params);

        // drop lp and course relation temporary table after query execution
        if (isset($tablename)) {
            $this->drop_table($tablename);
        }
        $filename = get_string('reportname', 'local_edwiserreports', array(
            "date" => date('d_M_y_h-t-s', time())
        ));
        // Download csv based on query result.
        $this->set_csv_header($filename);
        echo implode(",", array_values((array)$headers)). "\n";
        foreach ($records as $record) {
            unset($record->id);
            // Print export header
            echo implode(",", array_values((array)$record)). "\n";
        }
    }
    /**
     * Get filter based results
     * @param  [string] $lps               Learnig Programs
     * @param  [array] $fields            Checkboxes fields
     */
    public function get_filter_based_fields($lps, $fields) {
        // remove the lp fields if lp is not selected
        if ($lps == "") {
            $fields = array_filter($fields, function ($string) {
                return strpos($string, 'lp') === false;
            });
        }
        return $fields;
    }
    /**
     * Temporary table for lp and courses relation
     * @param  [string] $tablename [table name]
     * @param  [string] $lpdb      [learning programs join query]
     * @param  [array] $params    [params for learning programs join query]
     */
    public function create_temp_table($tablename, $lpdb, $params) {
        global $DB, $CFG;
        $dbman = $DB->get_manager();

        // create table schema
        $table = new \xmldb_table($tablename);
        $table->add_field('id', XMLDB_TYPE_INTEGER, 10, null, XMLDB_NOTNULL, XMLDB_SEQUENCE);
        $table->add_field('lpid', XMLDB_TYPE_INTEGER, 10, null, null, false);
        $table->add_field('courseid', XMLDB_TYPE_INTEGER, 10,null, null, false);
        $table->add_key('id', XMLDB_KEY_PRIMARY, array('id'));

        if ($dbman->table_exists($tablename)) {
            $dbman->drop_table($table);
        }

        $dbman->create_temp_table($table);
        // get courses from selected lps
        $sql = "SELECT id, courses FROM {wdm_learning_program} WHERE id ".$lpdb;
        $records = $DB->get_records_sql($sql,  $params);
        $tempArray = array();
        // iterate and add new entry in table for each course with respect to lp
        array_map(function($value) use (&$tempArray) {
            if ($value->courses != NULL) {
                $courseids = json_decode($value->courses);
                foreach ($courseids as $id) {
                    array_push($tempArray, array("lpid" => $value->id, "courseid" => $id));
                }
            }
        }, $records);

        $DB->insert_records($tablename, $tempArray);
        return true;
    }
    /**
     * Delete temporary created table
     * @param  String $tablename Table name
     */
    public function drop_table($tablename) {
        global $DB;

        $dbman = $DB->get_manager();

        $table = new \xmldb_table($tablename);

        if ($dbman->table_exists($tablename)) {
            $dbman->drop_table($table);
        }
    }

    /**
     * Create Query Fields by Filters
     * @param  [Array] $fields filtered fields
     */
    public function create_query_fields($fields) {
        // Get all the fields
        $allFields = \local_edwiserreports\output\elucidreport_renderable::get_report_fields();
        $allFields = array_values($allFields);
        $allFields = array_reduce($allFields, 'array_merge', array());
        // sort fields according to selected fields
        $header = array();
        $allFields = array_map(function($value) use ($fields, &$header) {
            if (in_array($value['key'], $fields) ) {
                $header[] = $value['value'];
                return $value['dbkey'].' as '.$value['key'];
            }
            return false;
        }, $allFields);
        // filter it and make a string
        $allFields = array_filter( $allFields);
        $allFields = implode(', ', $allFields);
        return array($allFields, $header);
    }

    /**
     * Render csv
     * @param  stdClass $data Filter object to get reports data
     * @return stdClass       Status of reports
     */
    public function export_csv_customreport_data($type, $filters, $startdate, $enddate) {
        // Reports filename
        if ($type == 'lps') {
            $filename = 'Custom_Lp_Reports_';
        } else {
            $filename = 'Custom_Course_Reports_';
        }

        // Default starttime
        if (!$startdate || $startdate == "") {
            $startdate = 0;
        } else {
            $filename .= date('d_m_Y', $startdate) . '_to_';
        }

        // Default end time
        if (!$enddate || $enddate == "") {
            $enddate = time();
        }

        $filename .= date('d_m_Y', $enddate) . '.csv';

        // Set Csv headers
        $this->set_csv_header($filename);

        // Calculate end date by getting 23:59:59 time
        // Added 23:59:59 to get end date
        $enddate += (24 * 60 * 60 - 1);

        // Explode data filter
        $filters = explode(",", $filters);

        // According to datatype perform operation
        switch($type) {
            case "lps":
                $export = $this->render_lps_report_exportable_data($filters, $startdate, $enddate);
                break;
            case "courses":
            default:
                $export = $this->render_courses_report_exportable_data($filters, $startdate, $enddate);
        }
    }

    /**
     * Render course exportable header
     * @return array Report Header
     */
    private function render_course_report_exportable_header() {
        // Plugin component
        $component = 'local_edwiserreports';

        // Header for reports
        $head = array();
        $head['username'] = get_string('username', $component);
        $head['coursename'] = get_string('coursename', $component);
        $head['enrolledon'] = get_string('enrolledon', $component);
        $head['category'] = get_string('category', $component);
        $head['completionsper'] = get_string('completionsper', $component);
        $head['completedactivities'] = get_string('completedactivities', $component);
        $head['firstname'] = get_string('firstname', $component);
        $head['lastname'] = get_string('lastname', $component);
        $head['email'] = get_string('email', $component);

        // Add custom fields header
        $this->inseart_custom_filed_header($head);

        // Print export header
        echo implode(",", array_values($head)). "\n";

        return $head;
    }

    /**
     * Render learning program report header
     * @return array Learning Program header
     */
    private function render_lp_report_exportable_header() {
        // Plugin component
        $component = 'local_edwiserreports';

        // Header for reports
        $head = array();
        $head['username'] = get_string('username', $component);
        $head['lpname'] = get_string('lpname', $component);
        $head['enrolledon'] = get_string('enrolledon', $component);
        $head['average'] = get_string('average', $component);
        $head['firstname'] = get_string('firstname', $component);
        $head['lastname'] = get_string('lastname', $component);
        $head['email'] = get_string('email', $component);
        $head['activitycompleted'] = get_string('completedactivity', $component);

        // Inseart custom fields as header
        $this->inseart_custom_filed_header($head);

        // Print export header
        echo implode(",", array_values($head)). "\n";

        return $head;
    }

    /**
     * Inseart custom field header in reports file
     * @param  [type] &$head [description]
     * @return [type]        [description]
     */
    private function inseart_custom_filed_header(&$head) {
        // Get all custom fields to add in header
        $customfields = profile_get_custom_fields();

        // Add custom fields
        foreach ($customfields as $key => $customfield) {
            $head[$customfield->shortname] = $customfield->name;
        }
    }

    /**
     * Inseart custom field data in reports file
     * @param  [type] &$data [description]
     * @return [type]        [description]
     */
    private function inseart_custom_filed_data(&$data, $userid) {
        global $DB;

        // Get customdata
        $customfieldsdata = profile_user_record($userid);
        foreach($customfieldsdata as $key => $customdata) {
            // Get field data
            $field = $DB->get_record('user_info_field', array(
                'shortname' => $key
            ));

            if ($field->datatype == 'dynamicmenu') {
                $dynamicdata = $DB->get_records_sql($field->param1);
                if ($customdata == 0) {
                    $data->$key = "";
                } else {
                    if (isset($dynamicdata[$customdata]) && $dynamicdata[$customdata] !== "") {
                        $data->$key = $dynamicdata[$customdata]->data;
                    } else {
                        $data->$key = "";
                    }
                }
            } else {
                $data->$key = '"' . $customdata . '"';
            }
        }
    }

    /**
     * Render course related exportable data
     * @param  array $courseids       Course Ids
     * @param  int $enrolstartdate    Enrolment Start Date
     * @param  int $enrolenddate      Enrolment End Date
     * @return array                  Array of exportable data
     */
    private function render_courses_report_exportable_data($courseids, $enrolstartdate, $enrolenddate) {
        global $DB;

        // Render course exportable header
        $head = $this->render_course_report_exportable_header();

        // Export course data from courses
        foreach ($courseids as $courseid) {
            // Get course and course context
            $course = get_course($courseid);
            $coursecontext = context_course::instance($course->id);

            // Get only enrolled students
            $users = course_progress_block::rep_get_enrolled_users($coursecontext, 'moodle/course:isincompletionreports');

            // Prepare reports for each students
            foreach ($users as $user) {
                // Get enrolment informations
                $enrolinfo = \local_edwiserreports\utility::get_course_enrolment_info($course->id, $user->id);

                // If startdate is less then the selected start date
                if ($enrolinfo->timecreated < $enrolstartdate) {
                    continue;
                }

                // If end date is set then
                if ($enrolinfo->timeend && $enrolinfo->timeend > $enrolenddate) {
                    continue;
                }


                // Get category
                $category = core_course_category::get($course->category);

                // Prepare data object
                $data = new stdClass();
                $data->firstname = $user->firstname;
                $data->lastname = $user->lastname;
                $data->email = $user->email;
                $data->username = $user->username;
                $data->enrolledon = date('d-M-y', $enrolinfo->timecreated);
                $data->coursename = $course->fullname;
                $data->category = $category->get_formatted_name();

                // Get completions data
                $completion = (object) \local_edwiserreports\utility::get_course_completion_info($course, $user->id);
                if ($completion && !empty($completion)) {
                    $data->completedactivities = '(' . $completion->completedactivities . '/' . $completion->totalactivities . ')';
                    $data->completionsper = $completion->progresspercentage . "%";
                } else {
                    $data->completedactivities = get_string('na', 'local_edwiserreports');
                    $data->completionsper = get_string('na', 'local_edwiserreports');
                }

                $this->inseart_custom_filed_data($data, $user->id);

                // Get appropreate data according to header
                $reportdata = $head;
                foreach($head as $key => $cell)  {
                    $reportdata[$key] = $data->$key;
                }

                // Rnder the report data
                echo implode(",", array_values($reportdata)) . "\n";
            }
        }

        // Return true
        return true;
    }

    /**
     * Render learning program related exportable data
     * @param  array $courseids       Course Ids
     * @param  int $enrolstartdate    Enrolment Start Date
     * @param  int $enrolenddate      Enrolment End Date
     * @return array                  Array of exportable data
     */
    private function render_lps_report_exportable_data($lpids, $enrolstartdate, $enrolenddate) {
        global $DB;
        $component = 'local_edwiserreports';

        // Render reports header
        $head = $this->render_lp_report_exportable_header();

        // Export course data from courses
        foreach ($lpids as $lpid) {
            // Get course and course context
            $table = 'wdm_learning_program';
            $lp = $DB->get_record($table, array("id" => $lpid, "visible" => true));

            // Get only enrolled students
            $enrolments = \local_edwiserreports\utility::get_lp_students($lpid);

            // Prepare reports for each students
            foreach ($enrolments as $enrolment) {

                // If startdate is less then the selected start date
                if ($enrolment->timeenroled < $enrolstartdate || $enrolment->timeenroled > $enrolenddate) {
                    continue;
                }

                // Get all course reports which is in learning program
                $completionavg = 0;
                $coursecount = 0;
                $completedactivities = 0;
                $totalactivities = 0;
                foreach(json_decode($lp->courses) as $courseid) {
                    // If course is not there then return from here
                    if (!$course = $DB->get_record('course', array('id' => $courseid))) {
                        continue;
                    }

                    // Get completions data
                    $completion = \local_edwiserreports\utility::get_course_completion_info($course, $enrolment->userid);

                    if ($completion && !empty($completion)) {
                        $completionavg += $completion['progresspercentage'];
                        $completedactivities += $completion['completedactivities'];
                        $totalactivities += $completion['totalactivities'];
                    }

                    // Increase course count
                    $coursecount++;
                }

                // Add completion report to the export array
                $user = core_user::get_user($enrolment->userid);
                $customfieldsdata = profile_user_record($user->id);

                // Prepare data object
                $data = new stdClass();
                $data->firstname = $user->firstname;
                $data->lastname = $user->lastname;
                $data->email = $user->email;
                $data->username = $user->username;
                $data->enrolledon = date('d-M-y', $enrolment->timeenroled);
                $data->lpname = $lp->name;
                $data->average = number_format($completionavg / $coursecount, 2) . "%";
                $data->activitycompleted = '(' . $completedactivities . '/' . $totalactivities . ')';

                // Inseart custom field data
                $this->inseart_custom_filed_data($data, $user->id);

                // Get appropreate data according to header
                $reportdata = $head;
                foreach($head as $key => $cell)  {
                    $reportdata[$key] = $data->$key;
                }

                // Render the report data
                echo implode(",", array_values($reportdata)) . "\n";
            }
        }

        // Return status
        return true;
    }
}
