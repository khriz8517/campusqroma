<?php

global $CFG;

require_once(dirname(__FILE__) . '/../../config.php');
require (dirname(__FILE__) . '/../../local/firmareporte/excel/PHPExcel/Classes/PHPExcel/IOFactory.php');
require_once($CFG->dirroot .'/user/profile/lib.php');

function getUserById($userId) {
    global $DB;

    $user = $DB->get_record('user', array('id' => $userId));
    return $user;
}

function firmareporte_task() {
    global $DB;
    global $CFG;

    $firmaDetail = $DB->get_records_sql("SELECT id, course, timecreated FROM {firma_detalle}");

    $cursoIds = array();

    foreach($firmaDetail as $firmaD) {
        if(date('Ymd') == date('Ymd', $firmaD->timecreated)) {
            $cursoIds[] = $firmaD->course;

        }
    }

    $cursoIds = array_unique($cursoIds);

    if(!empty($cursoIds)) {
        foreach($cursoIds as $cursoId) {

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

            $numAsist = 0;

            $firmaDetailCount = $DB->get_record_sql("SELECT COUNT(*) AS cant FROM {firma_detalle} WHERE course = ? GROUP BY userid", array($cursoId));

            $firmaElements = $DB->get_records_sql("SELECT max(timecreated) as createdtime FROM {firma_detalle} WHERE course = ? GROUP BY userid", array($cursoId));
            $firmaElements = array_keys($firmaElements);

            $firmaDetail = $DB->get_records_sql("SELECT * FROM {firma_detalle} WHERE course = ?", array($cursoId));

            //$numAsist = $firmaDetailCount->cant;
            $horasAll = $firma->horas_total;
            $capacitador = $firma->capacitador;;

            $datos = array();

            foreach($firmaDetail as $firmaDet) {
                if(date('Ymd') != date('Ymd', $firmaDet->timecreated)) {
                    continue;
                }
                if(!in_array($firmaDet->timecreated, $firmaElements)) {
                    continue;
                }
                $userObj = getUserById($firmaDet->userid);
                profile_load_custom_fields($userObj);

                $datos[] = [
                    'nombre' => $userObj->firstname . ' ' . $userObj->lastname,
                    'dni' => empty($firmaDet->dni) ? $userObj->profile['dni'] : $firmaDet->dni,
                    'empresa' => empty($firmaDet->empresa) ? 'Qroma' : $firmaDet->empresa,
                    'firma' => $CFG->dirroot. '/mod/firma/files/firmasdetail/'.$firmaDet->course.'_'.$firmaDet->userid.'/'.$firmaDet->image,
                    'observaciones' => "",
                ];
                $numAsist++;
            }

            $userId = $firma->userid;

            $user = getUserById($userId);
            profile_load_custom_fields($user);

            $firmaImg =  $CFG->dirroot. '/mod/firma/files/firmasbase/'.$firma->course.'/'.$firma->imagen;

            $nombre = $user->firstname . ' ' . $user->lastname;
            $cargo = $user->profile['cargo'];
            $fechaRes = date('d-m-Y');
            $firma =  $firmaImg;

            // crear objeto
            $objPHPExcel = new PHPExcel();
            $objReader = PHPExcel_IOFactory::createReader('Excel2007');
            // leer plantilla
            $objPHPExcel = $objReader->load(dirname(__FILE__) . '/excel/base.xlsx');
            // seleccionar la hoja uno
            $objPHPExcel->setActiveSheetIndex(0);

            //
            $objPHPExcel->getActiveSheet()->SetCellValue('J1', 'Código: '.$codigo);
            $objPHPExcel->getActiveSheet()->SetCellValue('J2', 'Version: '.$version);
            $objPHPExcel->getActiveSheet()->SetCellValue('J3', 'Fecha:   '.$date);

            //
            $objPHPExcel->getActiveSheet()->SetCellValue('B7', $razonSocial);
            $objPHPExcel->getActiveSheet()->SetCellValue('C7', $ruc);
            $objPHPExcel->getActiveSheet()->SetCellValue('D7', $domicilio);
            $objPHPExcel->getActiveSheet()->SetCellValue('J7', $cantTrabajadores);
            $objPHPExcel->getActiveSheet()->SetCellValue('K7', $numeroRegistro);

            if($tipe == "Inducción"){
                $objPHPExcel->getActiveSheet()->SetCellValue('D8', "X");
            }
            if($tipe == "Capacitación"){
                $objPHPExcel->getActiveSheet()->SetCellValue('F8', "X");
            }
            if($tipe == "Entrenamiento"){
                $objPHPExcel->getActiveSheet()->SetCellValue('I8', "X");
            }
            if($tipe == "Simulacro"){
                $objPHPExcel->getActiveSheet()->SetCellValue('K8', "X");
            }

            $objPHPExcel->getActiveSheet()->SetCellValue('D9', $tema);

            $objPHPExcel->getActiveSheet()->SetCellValue('C10', $dateCap);
            $objPHPExcel->getActiveSheet()->SetCellValue('E10', $horaIni);
            $objPHPExcel->getActiveSheet()->SetCellValue('G10', $horaFin);
            $objPHPExcel->getActiveSheet()->SetCellValue('I10', $numAsist);
            $objPHPExcel->getActiveSheet()->SetCellValue('K10', $horasAll);

            $objPHPExcel->getActiveSheet()->SetCellValue('E11', $capacitador);

            $fila = 13;

            foreach($datos as $data){
                $objPHPExcel->getActiveSheet()->SetCellValue('B'.$fila, $data['nombre']);
                $objPHPExcel->getActiveSheet()->SetCellValue('E'.$fila, $data['dni']);
                $objPHPExcel->getActiveSheet()->SetCellValue('G'.$fila, $data['empresa']);
                // $objPHPExcel->getActiveSheet()->SetCellValue('I'.$fila, $data['firma']);
                $objPHPExcel->getActiveSheet()->SetCellValue('J'.$fila, $data['observaciones']);

                $objDrawing = new PHPExcel_Worksheet_Drawing();
                $objDrawing->setName('Sample image');
                $objDrawing->setDescription('Sample image');
                $objDrawing->setPath($data['firma']);
                $objDrawing->setCoordinates('I'.$fila);
                $objDrawing->setWidth(157);
                $objDrawing->setHeight(30);
                $objDrawing->setWorksheet($objPHPExcel->getActiveSheet());

                $fila += 1;
            }

            $objPHPExcel->getActiveSheet()->SetCellValue('B45', $nombre);
            $objPHPExcel->getActiveSheet()->SetCellValue('F45', $cargo);
            $objPHPExcel->getActiveSheet()->SetCellValue('I45', $fechaRes);
            // $objPHPExcel->getActiveSheet()->SetCellValue('J45', $firma);

            //  agregar imagen
            // deifinir objeto para la firma
            $objDrawing = new PHPExcel_Worksheet_Drawing();
            $objDrawing->setName('Sample image');
            $objDrawing->setDescription('Sample image');

            $objDrawing->setPath($firma);
            $objDrawing->setCoordinates('J45');
            $objDrawing->setWidth(157);
            $objDrawing->setHeight(47);
            $objDrawing->setWorksheet($objPHPExcel->getActiveSheet());

            //Guardamos los cambios
            $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
            $name = $CFG->dirroot . '/local/firmareporte/files/'.date('Y-m-d').'_'.$cursoId.'.xlsx';
            $objWriter->save($name);
        }
    }
}