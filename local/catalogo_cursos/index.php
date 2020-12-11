<?php

global $CFG, $PAGE, $OUTPUT, $USER;

require_once '../qroma_front/classes/controller/QromaData.php';
require_once(dirname(__FILE__) . '/../../config.php');
require_once($CFG->dirroot . '/user/profile/lib.php');
include_once('../qroma_front/constants.php');

require_login();
$context = context_user::instance($USER->id);
$PAGE->set_context($context);

use LocalPages\Controller\QromaData as QromaDataController;

$qromaDataController = new QromaDataController();
$origen = $qromaDataController->getUserType()->profile['origen'];

if($origen == ORIGEN_NO_ASIGNADO) {
    header ("Location: " . 'https://www.campusqroma.com');
    exit();
}

$title = 'Home';
// Set up the page.
$url = new moodle_url("/local/catalogo_cursos/index.php");
$PAGE->set_url($url);
$PAGE->set_title('Catálogo de cursos');
$PAGE->requires->css(new moodle_url('../qroma_front/css/_base.css'));
$PAGE->requires->css(new moodle_url('../qroma_front/css/general.css'));
$PAGE->requires->jquery();

echo $OUTPUT->header();
include('shared_layout.html');

function includeHtmlOrigen($origen, $idCat){
    $qromaDataController = new QromaDataController();
    if($qromaDataController->obtenerSubcategorias($idCat)['type'] == 1) {
        include($origen . '/curso_d.html');
    } else if($qromaDataController->obtenerSubcategorias($idCat)['type'] == 2) {
        include($origen . '/curso_c.html');
    } else if($qromaDataController->obtenerSubcategorias($idCat)['type'] == 3) {
        include($origen . '/curso_a.html');
    }
}

switch($origen) {
    case ORIGEN_QROMA:
    case ORIGEN_TRICOLOR:
       includeHtmlOrigen('qroma', 1);
       $PAGE->requires->js(new moodle_url('../qroma_front/js/qroma/curso.js'));
       break;
    case ORIGEN_COLORCENTRO:
       includeHtmlOrigen('color_centro', 2);
       $PAGE->requires->js(new moodle_url('../qroma_front/js/color_centro/curso.js'));
       break;
    case ORIGEN_FERRETERIAS:
       includeHtmlOrigen('ferreterias', 3);
       $PAGE->requires->js(new moodle_url('../qroma_front/js/ferreterias/curso.js'));
       break;
    case ORIGEN_EXTERNOS:
       includeHtmlOrigen('externos', 13);
       $PAGE->requires->js(new moodle_url('../qroma_front/js/externos/curso.js'));
       break;
}

echo $OUTPUT->footer();