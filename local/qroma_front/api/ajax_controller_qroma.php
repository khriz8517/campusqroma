<?php

use core_completion\progress;

error_reporting(E_ALL);

require_once(dirname(__FILE__) . '/../../../config.php');

try {
    global $USER, $PAGE;
    $details = $_POST;
    $returnArr = array();

    if (!isset($_REQUEST['request_type']) || strlen($_REQUEST['request_type']) == false) {
        throw new Exception();
    }

    switch ($_REQUEST['request_type']) {
        case 'obtenerTestimonios':
            $returnArr = obtenerTestimonios();
            break;
        case 'obtenerSlider':
            $returnArr = obtenerSlider($_POST['ferreterias']);
            break;
        case 'obtenerCategorias':
            $returnArr = obtenerCategorias();
            break;
        case 'obtenerSubcategoriasByCat':
            $returnArr = obtenerSubcategoriasByCat($_POST['idCat']);
            break;
        case 'obtenerCursosByCat':
            $returnArr = obtenerCursosByCat($_POST['idCat']);
            break;
        case 'obtenerCursosBySearch':
            $returnArr = obtenerCursosBySearch($_POST['name'], $_POST['idCat']);
            break;
    }
} catch (Exception $e) {
    $returnArr['status'] = false;
    $returnArr['data'] = $e->getMessage();
}

header('Content-type: application/json');

echo json_encode($returnArr);
exit();

function obtenerSlider($ferreteria) {
    $slides = array();
    $sliderData = \theme_remui\sitehomehandler::get_slider_data();

    foreach($sliderData['slides'] as $slide) {
        $slide['background'] = str_replace('//www.campusqroma.com','', $slide['img']);

        $title = strip_tags($slide['img_txt']) ? strip_tags($slide['img_txt']) : false;
        $btnText =  $slide['btn_txt'] ?  $slide['btn_txt'] : false;

        $slides[] = ['title' => $title, 'btnText' => $btnText, 'background' => $slide['background'], 'url' => $slide['btn_link']];
    }

    if($ferreteria) {
        $slides = array_slice($slides,2,3);
    } else {
        $slides = array_slice($slides,0,2);
    }

    $response['status'] = true;
    $response['data'] = $slides;

    return $response;
}

function obtenerTestimonios() {
    $testimonios = array();
    $testimonialData = \theme_remui\sitehomehandler::get_testimonial_data();

    foreach($testimonialData['testimonials'] as $testimonial) {
        $testimonial['image'] = str_replace('//www.campusqroma.com','',$testimonial['image']);
        $testimonios[] = ['name' => $testimonial['name'],'content' => strip_tags($testimonial['text']), 'url' => $testimonial['image']];
    }

    $response['status'] = true;
    $response['data'] = $testimonios;

    return $response;
}

function obtenerCategorias() {
    global $DB;

    $categorias = $DB->get_records('course_categories');

    $response['status'] = true;
    $response['data'] = $categorias;

    return $response;
}

function obtenerSubcategoriasByCat($idCat) {
    global $USER;

    $btnClass = 'bg-info';
    $firstSubCategories = array();
    $firstCourses = array();
    $category = core_course_category::get($idCat);
    $subcategorias = $category->get_children();
    $cont = 0;

    if(!empty($subcategorias)) {
        foreach($subcategorias as $key=>$subcategory) {
            $active = '';

            if($cont == 0) {
                $active = 'active';
                $btnClass = 'bg-info';
            } elseif($cont == 1) {
                $btnClass = 'bg-success';
            } elseif($cont == 2) {
                $btnClass = 'bg-warning';
            } elseif($cont == 3) {
                $btnClass = 'bg-danger';
            }

            $totalCoursesGeneral = core_course_category::get($subcategory->id)->get_courses(
                array('recursive' => true, 'coursecontacts' => true, 'sort' => array('idnumber' => 1)));

            $firstSubCat = $subcategory->get_children();

            if($cont == 0) {
                $firstSubCategories[] = ['id' => $subcategory->id, 'name' => 'TODAS LAS SUBCATEGORÍAS', 'status' => 1];
                foreach($firstSubCat as $firstSub) {
                    $firstSubCategories[] = ['id' => $firstSub->id, 'name' => $firstSub->name, 'status' => $firstSub->visible];
                    $allcourses = core_course_category::get($firstSub->id)->get_courses(
                        array('recursive' => true, 'coursecontacts' => true, 'sort' => array('idnumber' => 1)));
                    foreach($allcourses as $course) {
                        $firstCourses[] = [
                            'name'=> $course->fullname,
                            'pais' => 'peru',
                            'url'=> 'course/view.php?id='.$course->id,
                            'img' => \theme_remui_coursehandler::get_course_image($course, 1)
                        ];
                    }
                }
            }
            $cont++;

            $totalCourses = count($totalCoursesGeneral);
            $totalCourses = $totalCourses > 1 || $totalCourses == 0 ? $totalCourses . ' CURSOS' : $totalCourses . ' CURSO';

            $subcategoriasData[] = [
                'id'=>$subcategory->id,
                'name' => $subcategory->name,
                'status' => $subcategory->visible,
                'btnClass' => $btnClass,
                'active'=> $active,
                'totalCourses' => $totalCourses
            ];
        }
        $response['data'] = $subcategoriasData;
        $response['dataSubCats'] = $firstSubCategories;
    } else {
        $allcourses = core_course_category::get($idCat)->get_courses(
            array('recursive' => true, 'coursecontacts' => true, 'sort' => array('idnumber' => 1)));

        foreach($allcourses as $course) {
            $courseObj = get_course($course->id);
            $percentage = round(progress::get_course_progress_percentage($courseObj, $USER->id));
            $firstCourses[] = [
                'name'=> $course->fullname,
                'pais' => 'peru',
                'url'=> 'course/view.php?id='.$course->id,
                'percentage' => $percentage ? $percentage : 0,
                'img' => \theme_remui_coursehandler::get_course_image($course, 1),
            ];
        }
    }


    $response['status'] = true;
    $response['dataCourses'] = $firstCourses;

    return $response;
}

function obtenerCursosByCat($idCat) {
    $courses = array();
    $allcourses = core_course_category::get($idCat)->get_courses(
        array('recursive' => true, 'coursecontacts' => true, 'sort' => array('idnumber' => 1)));

    foreach($allcourses as $course) {
        $courses[] = [
            'name'=> $course->fullname,
            'pais' => 'peru',
            'url'=> 'course/view.php?id='.$course->id,
            'img' => \theme_remui_coursehandler::get_course_image($course, 1)
        ];
    }

    $response['status'] = true;
    $response['data'] = $courses;
    $response['totalCourses'] = count($courses);

    return $response;
}

function obtenerCursosBySearch($name, $idCat) {
    global $USER;
    $courses = array();
    $allcourses = core_course_category::get($idCat)->get_courses(
        array('recursive' => true, 'coursecontacts' => true, 'sort' => array('idnumber' => 1)));

    foreach($allcourses as $course) {
        if(strpos(strtolower($course->fullname), strtolower($name)) !== false) {
            $percentage = round(progress::get_course_progress_percentage($course, $USER->id));
            $courses[] = [
                'name'=> $course->fullname,
                'pais' => 'peru',
                'url'=> 'course/view.php?id='.$course->id,
                'percentage'=> $percentage ? $percentage : 0,
                'img' => \theme_remui_coursehandler::get_course_image($course, 1)
            ];
        }
    }

    $response['status'] = true;
    $response['data'] = $courses;
    $response['totalCourses'] = count($courses);

    return $response;
}

















