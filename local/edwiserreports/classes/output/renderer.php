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

defined('MOODLE_INTERNAL') || die();

class local_edwiserreports_renderer extends plugin_renderer_base {
    /**
     * Renders the couse bundle view page.
     * @param  report_elucidreport $report  Object of elucid report renderable class
     * @return string  Html Structure of the view page
     */
    public function render_local_edwiserreports(\local_edwiserreports\elucidreport_renderable $report) {
        $templatecontext = $report->export_for_template($this);
        return $this->render_from_template('local_sitelocal/edwiserreports', $templatecontext);
    }
}

class report_activeusers_renderer extends plugin_renderer_base {
    /**
     * Renders the couse bundle view page.
     * @param  report_elucidreport $report  Object of elucid report renderable class
     * @return string  Html Structure of the view page
     */
    public function render_report_activeusers(\local_edwiserreports\activeusers_renderable $activeusers) {
        $templatecontext = $report->export_for_template($this);
        return $this->render_from_template('local_edwiserreports/activeusers', $templatecontext);
    }
}

class report_coursereport_renderer extends plugin_renderer_base {
    /**
     * Renders the couse bundle view page.
     * @param  report_elucidreport $report  Object of elucid report renderable class
     * @return string  Html Structure of the view page
     */
    public function render_report_courseprogress(\local_edwiserreports\coursereport_renderable $coursereport) {
        $templatecontext = $report->export_for_template($this);
        return $this->render_from_template('local_edwiserreports/coursereport', $templatecontext);
    }
}

class report_certificates_renderer extends plugin_renderer_base {
    /**
     * Renders the couse bundle view page.
     * @param  report_elucidreport $report  Object of elucid report renderable class
     * @return string  Html Structure of the view page
     */
    public function render_report_certificates(\local_edwiserreports\certificates_renderable $certificates) {
        $templatecontext = $report->export_for_template($this);
        return $this->render_from_template('local_edwiserreports/certificates', $templatecontext);
    }
}

class report_f2fsessions_renderer extends plugin_renderer_base {
    /**
     * Renders the couse bundle view page.
     * @param  report_elucidreport $report  Object of elucid report renderable class
     * @return string  Html Structure of the view page
     */
    public function render_report_f2fsessions(\local_edwiserreports\f2fsessions_renderable $f2fsessions) {
        $templatecontext = $report->export_for_template($this);
        return $this->render_from_template('local_edwiserreports/f2fsessions', $templatecontext);
    }
}

class report_lpstats_renderer extends plugin_renderer_base {
    /**
     * Renders the couse bundle view page.
     * @param  report_elucidreport $report  Object of elucid report renderable class
     * @return string  Html Structure of the view page
     */
    public function render_report_lpstats(\local_edwiserreports\lpstats_renderable $f2fsessions) {
        $templatecontext = $report->export_for_template($this);
        return $this->render_from_template('local_edwiserreports/lpstats', $templatecontext);
    }
}

class report_completion_renderer extends plugin_renderer_base {
    /**
     * Renders the Completion report.
     * @param  report_elucidreport $report  Object of completion renderable class
     * @return string  Html Structure of the view page
     */
    public function render_report_completion(\local_edwiserreports\completion_renderable $f2fsessions) {
        $templatecontext = $report->export_for_template($this);
        return $this->render_from_template('local_edwiserreports/completion', $templatecontext);
    }
}
