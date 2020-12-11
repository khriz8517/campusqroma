<?php

namespace LocalPages;

use stdClass;
use core_course_category;

require(__DIR__ . '/../../../../config.php');

class QromaData {
    public function __construct() {
    }

    public function ObtenerCursos() {
        $allCourses = core_course_category::get(0)->get_courses(array('recursive' => true));
        return $allCourses;
    }
}