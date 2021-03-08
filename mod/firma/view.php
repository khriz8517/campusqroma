<?php
require_once('../../config.php');
require_once('lib.php');

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

$PAGE->set_url('/mod/firma/view.php', array('id' => $cm->id));

$PAGE->requires->css(new moodle_url('../firma/css/signature-pad.css'));
$PAGE->requires->css(new moodle_url('../firma/css/ie9.css'));
$PAGE->requires->jquery();

$PAGE->set_title('Firma');

echo $OUTPUT->header();
include('home.html');
echo '<input type="hidden" id="signature-pad-curso" value="'.$cm->course.'">';
echo '<input type="hidden" id="signature-pad-mod" value="'.$cm->id.'">';
include('homefirma2.html');
echo $OUTPUT->footer();