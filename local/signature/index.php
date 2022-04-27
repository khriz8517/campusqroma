<?php
require_once(dirname(__FILE__) . '/../../config.php');

global $PAGE, $OUTPUT;

$title = 'Home';
// Set up the page.
$url = new moodle_url("/local/signature/index.php");
$PAGE->set_url($url);

$PAGE->requires->css(new moodle_url('../signature/css/signature-pad.css'));
$PAGE->requires->css(new moodle_url('../signature/css/ie9.css'));
$PAGE->requires->jquery();

$PAGE->set_title('Firma');

echo $OUTPUT->header();
include('home.html');
echo $OUTPUT->footer();