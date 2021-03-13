<?php
    require 'PHPExcel/Classes/PHPExcel/IOFactory.php';
    require 'datos.php';

    $idCurso = $_GET['idCurso'];

    // crear objeto
    $objPHPExcel = new PHPExcel();
    $objReader = PHPExcel_IOFactory::createReader('Excel2007');

    // leer plantilla
    $objPHPExcel = $objReader->load('base.xlsx');
    // seleccionar la hoja uno
    $objPHPExcel->setActiveSheetIndex(0);

    $fila = 2;

    $objPHPExcel->getActiveSheet()->SetCellValue('A'.$fila, $idCurso);
  
//    foreach($datos as $data){
//
//    $fila += 1;
//    }

    header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
    header('Content-Disposition: attachment;filename="prueba.xlsx"');
    header('Cache-Control: max-age=0');

    //Guardamos los cambios
    $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
    $objWriter->save('php://output');
    exit;
?>