<?php
require_once(dirname(__FILE__) . '/../../config.php');

global $PAGE, $OUTPUT;

$title = 'Home';
// Set up the page.
$url = new moodle_url("/local/qroma/dashboard.php");
$PAGE->set_url($url);

$PAGE->requires->css(new moodle_url('css/_base.css'));
$PAGE->requires->css(new moodle_url('css/general.css'));

$PAGE->requires->jquery();
$PAGE->requires->js(new moodle_url('js/main.js'));

echo $OUTPUT->header();
include('dashboard/index.php');
echo $OUTPUT->footer();