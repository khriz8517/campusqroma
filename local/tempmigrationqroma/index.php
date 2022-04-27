<?php
require_once(dirname(__FILE__) . '/../../config.php');

use core_completion\progress;

global $DB, $CFG;

$coursesReturn = array();
$courses = $DB->get_records('course', array('visible' => 1));

$cont = 0;

foreach($courses as $key=>$course) {
    if($course->id == 1) {
        continue;
    }
    $context = CONTEXT_COURSE::instance($course->id);
    $users = get_enrolled_users($context);
    foreach($users as $user) {
        $coursesReturn[$cont]['courseid'] = $course->id;
        $coursesReturn[$cont]['coursename'] = $course->fullname;
        $coursesReturn[$cont]['coursename'] = $course->fullname;
        $coursesReturn[$cont]['userid'] = $user->id;
        $coursesReturn[$cont]['progress'] = progress::get_course_progress_percentage($course, $user->id);
        $cont++;
    }
}

foreach ($coursesReturn as $cr) {
    $courseObj = $DB->get_record('qroma_course_user_tmp', array('courseid' => $cr['courseid'], 'userid' => $cr['userid']));
    if(empty($courseObj)) {
        $newCourseObj = new stdClass();
        $newCourseObj->courseid = $cr['courseid'];
        $newCourseObj->coursename = $cr['coursename'];
        $newCourseObj->userid = $cr['userid'];
        $newCourseObj->progress = $cr['progress'];
        $newCourseObj->created_at = date("Y-m-d H:i:s");
        $newCourseObj->updated_at = date("Y-m-d H:i:s");
        $DB->insert_record('qroma_course_user_tmp', $newCourseObj);
    } else {
        $newCourseObj = new stdClass();
        $newCourseObj->id = $courseObj->id;
        $newCourseObj->progress = $cr['progress'];
        $newCourseObj->updated_at = date("Y-m-d H:i:s");
        $DB->update_record('qroma_course_user_tmp', $newCourseObj);
    }
}

$users = $DB->get_records('user', array('deleted' => 0));

require_once($CFG->dirroot . '/user/profile/lib.php');

foreach($users as $key=>$user) {

    if(trim($user->firstname.$user->lastname) == '') {
        continue;
    }

    profile_load_custom_fields($user);
    $origen = $user->profile['origen'];
    $area = trim($user->profile['area']);
    $direccion = trim($user->profile['direccion']);
    $tipo_empleado = trim($user->profile['tipo_empleado']);
    $cargo = trim($user->profile['cargo']);
    $celular = trim($user->profile['celular']);
    $codigo = trim($user->profile['codigo']);
    $dni = trim($user->profile['dni']);
    $jefe = trim($user->profile['jefe']);

    $userData = $DB->get_record('qroma_user_tmp', array('userid' => $user->id));

    $newUserObj = new stdClass();
    $newUserObj->userid = $user->id;
    $newUserObj->username = $user->firstname . ' ' . $user->lastname;
    $newUserObj->email = $user->email;
    $newUserObj->origen = $origen;
    $newUserObj->area = $area;
    $newUserObj->direccion = $direccion;
    $newUserObj->tipo_empleado = $tipo_empleado;
    $newUserObj->cargo = $cargo;
    $newUserObj->celular = $celular;
    $newUserObj->codigo = $codigo;
    $newUserObj->dni = $dni;
    $newUserObj->jefe= $jefe;
    $newUserObj->updated_at = date("Y-m-d H:i:s");

    if(empty($userData)) {
        $newUserObj->created_at = date("Y-m-d H:i:s");
        $DB->insert_record('qroma_user_tmp', $newUserObj);
    } else {
        $newUserObj->id = $userData->id;
        $DB->update_record('qroma_user_tmp', $newUserObj);
    }
}