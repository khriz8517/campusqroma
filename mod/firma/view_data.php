<?php
require_once('../../config.php');
require_once('lib.php');

global $CFG, $USER, $PAGE;

$PAGE->set_url('/mod/firma/view_data.php');

$PAGE->requires->css(new moodle_url('../firma/css/firma.css'));
$PAGE->requires->jquery();

$PAGE->set_title('Firma - ingresar datos');

echo $OUTPUT->header();
echo '<input type="hidden" id="cmId" value="'.$_GET['id'].'">';
include('home_data.html');
echo $OUTPUT->footer();