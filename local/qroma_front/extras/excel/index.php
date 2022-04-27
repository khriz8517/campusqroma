<?php
	//libreria phpExcel
  require 'PHPExcel/Classes/PHPExcel/IOFactory.php';
  // variables para rellenar datos

    $cursoId = $_GET['cursoId'];

	require 'datos.php';
	// crear objeto
  $objPHPExcel = new PHPExcel();
  $objReader = PHPExcel_IOFactory::createReader('Excel2007');
  // leer plantilla
	$objPHPExcel = $objReader->load('base.xlsx');
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

  header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
  header('Content-Disposition: attachment;filename="Reporte_firma_' . $cursoId . '.xlsx"');
  header('Cache-Control: max-age=0');

	//Guardamos los cambios
	$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
  $objWriter->save('php://output');
  exit;
?>