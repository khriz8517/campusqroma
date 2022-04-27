<?php
require_once(dirname(__FILE__) . '/../../config.php');

global $DB;

$firmaDetail = $DB->get_records_sql("SELECT id, course, timecreated FROM {firma_detalle}");

$cursoIds = array();

foreach($firmaDetail as $firmaD) {
    if(date('Ymd') == date('Ymd', $firmaD->timecreated)) {
        $cursoIds[] = $firmaD->course;

    }
}

$cursoIds = array_unique($cursoIds);

exec("C:/php/php.exe F:/campusqroma/moodle/local/firmareporte/excel/index.php?cursoId=28");
var_dump('se ejecuto con exito');