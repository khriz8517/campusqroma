<?php
require_once('../../config.php');
require_once('lib.php');

global $CFG, $USER;

require_once($CFG->dirroot . '/user/profile/lib.php');

$id = required_param('id', PARAM_INT);    // Course Module ID

if (!$cm = get_coursemodule_from_id('firma', $id)) {
    print_error('Course Module ID was incorrect'); // NOTE this is invalid use of print_error, must be a lang string id
}
if (!$course = $DB->get_record('course', array('id'=> $cm->course))) {
    print_error('course is misconfigured');  // NOTE As above
}
if (!$certificate = $DB->get_record('firma', array('id'=> $cm->instance))) {
    print_error('course module is incorrect'); // NOTE As above
}

if(
    ($USER->profile['origen'] != 'Qroma' && $USER->profile['origen'] != 'Tricolor')
    && $_GET['emp'] == ''
) {
    redirect('view_data.php?id='.$cm->id);
} else if($USER->profile['origen'] == 'Qroma' || $USER->profile['origen'] == 'Tricolor') {
    $emp = $USER->profile['origen'];
    $dni = $USER->profile['dni'];
} else if($_GET['emp'] != '') {
    $emp = $_GET['emp'];
    $dni = $_GET['dni'];
}

$PAGE->set_url('/mod/firma/view.php', array('id' => $cm->id));

$PAGE->requires->css(new moodle_url('../firma/css/signature-pad.css'));
$PAGE->requires->css(new moodle_url('../firma/css/ie9.css'));
$PAGE->requires->css(new moodle_url('../firma/css/firma.css'));
$PAGE->requires->css(new moodle_url('../firma/css/firma_panel.css'));
$PAGE->requires->jquery();

$PAGE->set_title('Firma');

$info = $DB->get_record_sql("SELECT * FROM (
  SELECT
    ROW_NUMBER() OVER (ORDER BY id ASC) AS rownumber,
    course, name, titulo, descripcion
  FROM {firma}
) AS foo
WHERE rownumber = ? AND course = ?", array($cm->instance, $cm->course));

echo $OUTPUT->header();
include('home.html');
echo '<input type="hidden" id="signature-pad-curso" value="'.$cm->course.'">';
echo '<input type="hidden" id="signature-pad-mod" value="'.$cm->id.'">';
echo '<input type="hidden" id="signature-pad-emp" value="'.$emp.'">';
echo '<input type="hidden" id="signature-pad-dni" value="'.$dni.'">';
echo '<h2 class="text-align-center q-big-blue-text">'. $info->name .'</h2>';
echo '<h3 class="text-align-center q-small-blue-text">'. $info->descripcion .'</h3>';
include('homefirma2.html');
echo $OUTPUT->footer();