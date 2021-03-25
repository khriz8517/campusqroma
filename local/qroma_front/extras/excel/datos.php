<?php

global $CFG, $DB;

require_once('../../../../user/profile/lib.php');
require_once('../../../../config.php');

function getUserById($userId) {
    global $DB;

    $user = $DB->get_record('user', array('id' => $userId));
    return $user;
}

$firma = $DB->get_record_sql("SELECT * FROM {firma} WHERE course = ? ORDER BY timecreated DESC", array($cursoId));

$codigo = "SGI-F-32";
$version = "00";
$date = date('d-m-Y');

$razonSocial = 'CPPQ S.A.';
$ruc = '20100073723';
$domicilio = 'Fab. de productos Quimicos - Resinas y productos del hogar';
$cantTrabajadores = $firma->nro_trabajadores;
$numeroRegistro = $firma->nro_registro;
// el tipo puede ser Inducción - Capcitación - Entrenamiento - Simulacro

$tipe = "Inducción";

switch($firma->tipo) {
    case 1:
        $tipe = "Inducción";
        break;
    case 2:
        $tipe = "Capacitación";
        break;
    case 3:
        $tipe = "Entrenamiento";
        break;
    case 4:
        $tipe = "Simulacro";
        break;
}

$curso= $DB->get_record('course',array('id'=>$cursoId));

$tema = $curso->fullname;

$dateCap = date('d-m-Y');
$horaIni = $firma->hora_inicio;
$horaFin = $firma->hora_fin;

$firmaDetailCount = $DB->get_record_sql("SELECT COUNT(*) AS cant FROM {firma_detalle} WHERE course = ? GROUP BY userid", array($cursoId));

$firmaElements = $DB->get_records_sql("SELECT max(timecreated) as createdtime FROM {firma_detalle} WHERE course = ? GROUP BY userid", array($cursoId));
$firmaElements = array_keys($firmaElements);

$firmaDetail = $DB->get_records_sql("SELECT * FROM {firma_detalle} WHERE course = ?", array($cursoId));

$numAsist = $firmaDetailCount->cant;
$horasAll = $firma->horas_total;
$capacitador = $firma->capacitador;;

$datos = array();

foreach($firmaDetail as $firmaDet) {
    if(!in_array($firmaDet->timecreated, $firmaElements)) {
        continue;
    }
    $userObj = getUserById($firmaDet->userid);
    profile_load_custom_fields($userObj);

    $datos[] = [
        'nombre' => $userObj->firstname . ' ' . $userObj->lastname,
        'dni' => empty($firmaDet->dni) ? $userObj->profile['dni'] : $firmaDet->dni,
        'empresa' => empty($firmaDet->empresa) ? 'Qroma' : $firmaDet->empresa,
        'firma' => dirname(__DIR__,4).'/mod/firma/files/firmasdetail/'.$firmaDet->course.'_'.$firmaDet->userid.'/'.$firmaDet->image,
        'observaciones' => "",
    ];
}

$userId = $firma->userid;

$user = getUserById($userId);
profile_load_custom_fields($user);

$firmaImg =  dirname(__DIR__,4).'/mod/firma/files/firmasbase/'.$firma->course.'/'.$firma->imagen;

$nombre = $user->firstname . ' ' . $user->lastname;
$cargo = $user->profile['cargo'];
$fechaRes = date('d-m-Y');
$firma =  $firmaImg;
?>