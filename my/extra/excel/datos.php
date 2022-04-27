<?php

global $CFG, $DB;

require_once('../../../user/profile/lib.php');
require_once('../../../config.php');
require_once($CFG->dirroot . '/lib/gradelib.php');
require_once($CFG->dirroot . '/enrol/externallib.php');
require_once($CFG->dirroot. '/course/lib.php');


function obtenerCumplimiento($courseId, $userId) {
    global $DB;
    $quiz = $DB->get_records_sql("select * from {quiz} q where q.course = ?", array($courseId));
    $courseCompletion = $DB->get_records_sql("select * from {course_completions} c where c.course = ? and c.userid = ?", array($courseId, $userId));

    $quizIdInicio = array_shift($quiz);
    $quizIdFin = end($quiz);

    $inicial = 0;
    $final = 0;

    $inicial = grade_get_grades($courseId, 'mod', 'quiz', $quizIdInicio->id, $userId);
    $final = grade_get_grades($courseId, 'mod', 'quiz', $quizIdFin->id, $userId);

    $inicialGrade = array_shift(array_shift($inicial->items)->grades)->grade;
    $finalGrade = array_shift(array_shift($final->items)->grades)->grade;

    $inicial = $inicialGrade != '' ? $inicialGrade : '-';
    $final = $finalGrade  != '' ? $finalGrade : '-';

    $timeCompleted = array_shift($courseCompletion)->timecompleted;
    $timeCompleted = $timeCompleted != NULL ? date('d/m/Y', $timeCompleted) : '-';

    if($inicial != '-' && $final != '-' && $timeCompleted != '-') {
        $cumplimiento = 'Finalizado';
    } else {
        $cumplimiento = '-';
    }

    return array($cumplimiento, $timeCompleted);
}

$usuarios = $DB->get_records_sql("
select b.userid, b.codigo_trabajador, b.dni, b.username, b.cargo, b.email, b.direccion, b.area 
from {qroma_course_user_tmp} a join 
{qroma_user_tmp} b on a.userid=b.userid 
WHERE a.courseid = ?", array($idCurso));

$datos = array();

foreach($usuarios as $usuario) {
    $datos[] = [
        'codigo' => $usuario->codigo_trabajador ?? '-',
        'documento' => $usuario->dni ?? '-',
        'nombre' => $usuario->username ?? '-',
        'posicion' => $usuario->cargo ?? '-',
        'correo' => $usuario->email ?? '-',
        'direccion' => $usuario->direccion ?? '-',
        'area' => $usuario->area ?? '-',
        'cumplimiento' => obtenerCumplimiento($idCurso, $usuario->userid)[0],
        'fecha' => obtenerCumplimiento($idCurso, $usuario->userid)[1]
    ];
}

?>