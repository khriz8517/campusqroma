<?php
global $CFG;
require_once('../../../config.php');

// Get real path for our folder
$rootPath = $CFG->dirroot . '/local/firmareporte/files';

$idCurso = $_GET['idCurso'] ?? null;
$fecha = $_GET['fecha'] ?? null;

// Initialize archive object
$zip = new ZipArchive();
$tmpFile = 'myZip.zip';
$zip->open($tmpFile, ZipArchive::CREATE | ZipArchive::OVERWRITE);

// Create recursive directory iterator
/** @var SplFileInfo[] $files */
$files = new RecursiveIteratorIterator(
    new RecursiveDirectoryIterator($rootPath),
    RecursiveIteratorIterator::LEAVES_ONLY
);

function get_string_between($string, $start, $end){
    $string = ' ' . $string;
    $ini = strpos($string, $start);
    if ($ini == 0) return '';
    $ini += strlen($start);
    $len = strpos($string, $end, $ini) - $ini;
    return substr($string, $ini, $len);
}

$cont = 0;
$contfiles = 0;

foreach ($files as $name => $file) {

    if(!empty($idCurso) && !empty($fecha)) {
        $idGet = get_string_between($name,'_','.xlsx');
        $fechaGet =  get_string_between($name,
            $rootPath."\\",
            '_');

        if($idGet != $idCurso || $fechaGet != $fecha) {
            $cont++;
            continue;
        }
    }

    if (!$file->isDir()) {
        $contfiles++;
        // Get real and relative path for current file
        $filePath = $file->getRealPath();
        $relativePath = substr($filePath, strlen($rootPath) + 1);

        // Add current file to archive
        $zip->addFile($filePath, $relativePath);
    }
}

if($contfiles == 0) {
    echo 'Este curso no tiene reportes de firma en la fecha indicada';
    echo '<br><a href="/my">Regresar a la página anterior</a>';
    exit;
}

// Zip archive will be created only after closing object
$zip->close();

echo 'Archivo creado!';
ob_clean();
ob_end_flush();
header('Content-disposition: attachment; filename=ReporteFirmasFecha_'. $fecha . '_' . $idCurso .'.zip');
header('Content-type: application/zip');
readfile($tmpFile);
// remove zip file is exists in temp path
unlink($tmpFile);