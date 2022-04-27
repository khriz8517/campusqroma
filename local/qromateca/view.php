<?php
require_once(dirname(__FILE__) . '/../../config.php');

global $PAGE, $OUTPUT;

$title = 'Home';
// Set up the page.
$url = new moodle_url("/local/qromateca/view.php");
$PAGE->set_url($url);

$PAGE->requires->css(new moodle_url('../qromateca/css/base.css'));
$PAGE->requires->css(new moodle_url('../qroma_front/css/_base.css'));
$PAGE->requires->css(new moodle_url('../qroma_front/css/general.css'));
$PAGE->requires->jquery();
$PAGE->requires->js(new moodle_url('../qromateca/js/view.js'));

$PAGE->set_title('Qromateca');

echo $OUTPUT->header();
include('shared_layout.html');
include('view.html');
echo $OUTPUT->footer();