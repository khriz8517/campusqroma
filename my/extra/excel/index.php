<?php
    require 'PHPExcel/Classes/PHPExcel/IOFactory.php';

    $idCurso = $_GET['idCurso'];

    require 'datos.php';

    global $DB;

    // crear objeto
    $objPHPExcel = new PHPExcel();
    $objReader = PHPExcel_IOFactory::createReader('Excel2007');

    // leer plantilla
    $objPHPExcel = $objReader->load('base.xlsx');
    // seleccionar la hoja uno
    $objPHPExcel->setActiveSheetIndex(0);

    $fila = 2;

    foreach($datos as $data){
        $objPHPExcel->getActiveSheet()->SetCellValue('A'.$fila, $data['codigo']);
        $objPHPExcel->getActiveSheet()->SetCellValue('B'.$fila, $data['documento']);
        $objPHPExcel->getActiveSheet()->SetCellValue('C'.$fila, $data['nombre']);
        $objPHPExcel->getActiveSheet()->SetCellValue('D'.$fila, $data['posicion']);
        $objPHPExcel->getActiveSheet()->SetCellValue('E'.$fila, $data['correo']);
        $objPHPExcel->getActiveSheet()->SetCellValue('F'.$fila, $data['direccion']);
        $objPHPExcel->getActiveSheet()->SetCellValue('G'.$fila, $data['area']);
        $objPHPExcel->getActiveSheet()->SetCellValue('H'.$fila, $data['cumplimiento']);
        $objPHPExcel->getActiveSheet()->SetCellValue('I'.$fila, $data['fecha']);
        $fila += 1;
    }

    $sql = "SELECT fullname FROM {course} WHERE id = ?";
    $curso = $DB->get_record_sql($sql, array($idCurso));

    header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
    header('Content-Disposition: attachment;filename="Reporte Excel - '. $curso->fullname .'.xlsx"');
    header('Cache-Control: max-age=0');

    //Guardamos los cambios
    $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
    $objWriter->save('php://output');
    exit;
?>