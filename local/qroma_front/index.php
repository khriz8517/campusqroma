<?php
require_once('config.php');

global $PAGE, $OUTPUT;

$title = 'Home';
// Set up the page.
$url = new moodle_url("/local/qroma_front/index.php");
$PAGE->set_url($url);

$PAGE->requires->css(new moodle_url('css/_base.css'));
$PAGE->requires->css(new moodle_url('css/general.css'));

$PAGE->requires->jquery();
$PAGE->requires->js(new moodle_url('js/main.js'));

echo $OUTPUT->header();
include('home.php');
echo $OUTPUT->footer();