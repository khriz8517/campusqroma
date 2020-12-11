<?php
require_once(dirname(__FILE__) . '/../../config.php');

global $PAGE, $OUTPUT;

$title = 'Home';
// Set up the page.
$url = new moodle_url("/local/dashboard/index.php");
$PAGE->set_url($url);

$PAGE->requires->css(new moodle_url('../qroma_front/css/_base.css'));
$PAGE->requires->css(new moodle_url('../qroma_front/css/general.css'));
$PAGE->requires->jquery();
$PAGE->requires->js(new moodle_url('../qroma_front/js/main.js'));

$PAGE->set_title('Dashboard');

echo $OUTPUT->header();
include('home.html');
echo $OUTPUT->footer();