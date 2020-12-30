<?php

use core_completion\progress;
//use theme_remui\usercontroller;
use block_xp\local\xp\level_with_name;
use block_xp\local\xp\level_with_badge;

error_reporting(E_ALL);

require_once(dirname(__FILE__) . '/../../../config.php');

const QROMATECA_DOCUMENT_TYPE = 1;
const QROMATECA_VIDEO_TYPE = 2;
const QROMATECA_PAGE_TYPE = 3;

try {
    global $USER, $PAGE;
    $details = $_POST;
    $returnArr = array();

    if (!isset($_REQUEST['request_type']) || strlen($_REQUEST['request_type']) == false) {
        throw new Exception();
    }

    switch ($_REQUEST['request_type']) {
        case 'obtenerUsuario':
            $returnArr = obtenerUsuario();
            break;
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
        case 'obtenerQromatecas':
            $returnArr = obtenerQromatecas();
            break;
        case 'obtenerQromateca':
            $returnArr = obtenerQromateca($_POST['id']);
            break;
        case 'guardarQromateca':
            $returnArr = guardarQromateca($_POST);
            break;
        case 'actualizarVistas':
            $returnArr = actualizarVistas($_POST['id']);
            break;
        case 'aprobar':
            $returnArr = aprobar($_POST['id']);
            break;
        case 'desaprobar':
            $returnArr = desaprobar($_POST['id']);
            break;
        case 'cargarComentarios':
            $returnArr = cargarComentarios($_POST['id']);
            break;
        case 'crearComentario':
            $returnArr = crearComentario($_POST);
            break;
        case 'obtenerQromatecasSorted':
            $returnArr = obtenerQromatecasSorted($_POST['id']);
            break;
        case 'eliminarComentario':
            $returnArr = eliminarComentario($_POST['id']);
            break;
    }
} catch (Exception $e) {
    $returnArr['status'] = false;
    $returnArr['data'] = $e->getMessage();
}

header('Content-type: application/json');

echo json_encode($returnArr);
exit();

function convertDateToSpanish($timestamp) {
    setlocale(LC_TIME, 'es_ES', 'Spanish_Spain', 'Spanish');
    return strftime("%d de %B de %Y", $timestamp);
}

function formatCurrentTime($time) {
    if (isset($time)) {
        $time = strtotime(date('Y-m-d H:i', $time));
    }
    return $time;
}

function timeSince($original) {
    $original = formatCurrentTime($original);

    $ta = array(
        array(31536000, "Año", "Años"),
        array(2592000, "Mes", "Meses"),
        array(604800, "Semana", "Semanas"),
        array(86400, "Día", "Días"),
        array(3600, "Hora", "Horas"),
        array(60, "Minuto", "Minutos"),
        array(1, "Segundo", "Segundos")
    );
    $since = time() - $original;
    $res = "";
    $lastkey = 0;
    for ($i = 0; $i < count($ta); $i++) {
        $cnt = floor($since / $ta[$i][0]);
        if ($cnt != 0) {
            $since = $since - ($ta[$i][0] * $cnt);
            if ($res == "") {
                $res .= ($cnt == 1) ? "1 {$ta[$i][1]}" : "{$cnt} {$ta[$i][2]}";
                $lastkey = $i;
            } else if ($ta[0] >= 60 && ($i - $lastkey) == 1) {
                $res .= ($cnt == 1) ? " y 1 {$ta[$i][1]}" : " y {$cnt} {$ta[$i][2]}";
                break;
            } else {
                break;
            }
        }
    }
    return $res;
}


function getUserImage() {
    global $USER;
    return '/user/pix.php/'.$USER->id.'/f1.jpg';
}

function obtenerLevelPropertyValue($level, $property) {
    $returnedValue = '';

    switch($property) {
        case 'name':
            $name = $level instanceof level_with_name ? $level->get_name() : null;
            if (empty($name)) {
                $name = get_string('levelx', 'block_xp', $level->get_level());
            }
            $returnedValue = $name;
            break;
    }
    return $returnedValue;
}

function getLevelBadge($level, $small) {
    $levelnum = $level->get_level();

    if($small == 1) {
        $customClass = 'qroma-block_xp-level';
    } else {
        $customClass = 'qroma-block_xp-level-2';
    }

    $classes = $customClass . ' block_xp-level level-' . $levelnum;
    $label = get_string('levelx', 'block_xp', $levelnum);
    $classes .= ' d-badge';

    $html = '';
    if ($level instanceof level_with_badge && ($badgeurl = $level->get_badge_url()) !== null) {
        $html .= html_writer::tag(
            'div',
            html_writer::empty_tag('img', ['src' => $badgeurl,
                'alt' => $label, 'class'=> 'd-badge-img']),
            ['class' => $classes . ' level-badge', 'style' => 'height: 75px;']
        );
    } else {
        $html .= html_writer::tag('div', $levelnum, ['class' => $classes, 'aria-label' => $label]);
    }
    return $html;
}

function getUserLevel($small) {
    global $USER;

    $world = \block_xp\di::get('course_world_factory')->get_world(1);
    $state = $world->get_store()->get_state($USER->id);
    $widget = new \block_xp\output\xp_widget($state, [], null, []);
    $level = $widget->state->get_level();

    //Get data
    $levelName = obtenerLevelPropertyValue($level, 'name');
    $xp = $widget->state->get_xp();

    $levelInfo = array('levelName' => $levelName, 'xp' =>$xp, 'img' => getLevelBadge($level, $small));

    return $levelInfo;
}

function obtenerUsuario() {
    global $USER;
    $userArr = array(
        'photo' => getUserImage(),
        'name' => strtoupper($USER->firstname . ' ' . $USER->lastname),
        'levelImage' => getUserLevel(1)['img'],
        'points' => getUserLevel(1)['xp'],
        'dateReg' => convertDateToSpanish($USER->firstaccess),
    );

    $response['status'] = true;
    $response['data'] = $userArr;

    return $response;
}

function obtenerSlider($ferreteria) {
    $slides = array();
    $sliderData = \theme_remui\sitehomehandler::get_slider_data();

    foreach($sliderData['slides'] as $slide) {
        $slide['background'] = str_replace('//www.campusqroma.com','', $slide['img']);

        $title = strip_tags($slide['img_txt']) ? strip_tags($slide['img_txt']) : false;
        $btnText =  $slide['btn_txt'] ?  $slide['btn_txt'] : false;

        $slides[] = ['title' => $title, 'btnText' => $btnText, 'background' => $slide['background'], 'url' => $slide['btn_link']];
    }

    if(isset($ferreteria) && $ferreteria) {
        $slides = array_slice($slides,3,2);
    } else {
        $slides = array_slice($slides,0,3);
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

function obtenerQromatecas() {
    global $DB, $USER;
    $data = $DB->get_records_sql("SELECT * FROM {qromateca} ORDER BY vistas desc");

    $qromatecas = !empty($data) ? $data : array();

    $icon = '';

    $isManager = 1;
    $personalcontext = context_user::instance($USER->id);
    if (!has_capability('tool/policy:managedocs', $personalcontext)) {
        $isManager = 0;
    }

    foreach($qromatecas as $qromateca) {

        if(!$isManager && $qromateca->estado_aprobacion == 0) {
            continue;
        }

        switch($qromateca->tipo) {
            case QROMATECA_DOCUMENT_TYPE:
                $icon = 'clip.svg';
                break;
            case QROMATECA_VIDEO_TYPE:
                $icon = 'play.svg';
                break;
            case QROMATECA_PAGE_TYPE:
                $icon = 'mapamundi.svg';
                break;
        }

        $user = $DB->get_record('user', array('id' => $qromateca->id_usuario));
        $cantComentarios = count($DB->get_records_sql("SELECT * FROM {qromateca_comentarios} WHERE id_qromateca = ?", array($qromateca->id)));

        $returnArr[] = [
            'id'=> $qromateca->id,
            'nombre'=> $qromateca->nombre,
            'img'=> '/local/qromateca/files/' . $qromateca->imagen,
            'icon' => $icon,
            'user' => $user->firstname . ' ' . $user->lastname,
            'vistas' => $qromateca->vistas,
            'gestor' => $isManager,
            'estado_aprobacion' => $qromateca->estado_aprobacion,
            'cant_comentarios' => $cantComentarios
        ];
    }

    $response['status'] = true;
    $response['data'] = $returnArr;

    return $response;
}

function obtenerQromatecasSorted($id) {
    global $DB, $USER;

    if($id == 1) {
        $data = $DB->get_records_sql("SELECT * FROM {qromateca} ORDER BY vistas desc");
    } elseif($id == 2) {
        $data = $DB->get_records_sql("SELECT * FROM {qromateca} ORDER BY created_at desc");
    }


    $qromatecas = !empty($data) ? $data : array();

    $icon = '';

    $isManager = 1;
    $personalcontext = context_user::instance($USER->id);
    if (!has_capability('tool/policy:managedocs', $personalcontext)) {
        $isManager = 0;
    }

    foreach($qromatecas as $qromateca) {

        if(!$isManager && $qromateca->estado_aprobacion == 0) {
            continue;
        }

        switch($qromateca->tipo) {
            case QROMATECA_DOCUMENT_TYPE:
                $icon = 'clip.svg';
                break;
            case QROMATECA_VIDEO_TYPE:
                $icon = 'play.svg';
                break;
            case QROMATECA_PAGE_TYPE:
                $icon = 'mapamundi.svg';
                break;
        }

        $user = $DB->get_record('user', array('id' => $qromateca->id_usuario));
        $cantComentarios = count($DB->get_records_sql("SELECT * FROM {qromateca_comentarios} WHERE id_qromateca = ?", array($qromateca->id)));

        $returnArr[] = [
            'id'=> $qromateca->id,
            'nombre'=> $qromateca->nombre,
            'img'=> '/local/qromateca/files/' . $qromateca->imagen,
            'icon' => $icon,
            'user' => $user->firstname . ' ' . $user->lastname,
            'vistas' => $qromateca->vistas,
            'gestor' => $isManager,
            'estado_aprobacion' => $qromateca->estado_aprobacion,
            'cant_comentarios' => $cantComentarios
        ];
    }

    $response['status'] = true;
    $response['data'] = $returnArr;

    return $response;
}

function obtenerQromateca($id) {
    global $DB, $USER;

    $isManager = 1;
    $personalcontext = context_user::instance($USER->id);
    if (!has_capability('tool/policy:managedocs', $personalcontext)) {
        $isManager = 0;
    }

    $data = $DB->get_record_sql("SELECT * FROM {qromateca} WHERE id = ?", array($id));
    $qromateca = !empty($data) ? $data : array();

    if(!$isManager && $qromateca->estado_aprobacion == 0) {
        $response['status'] = false;
        return $response;
    }

    $user = $DB->get_record('user', array('id' => $qromateca->id_usuario));

    $isManager = 1;
    $personalcontext = context_user::instance($USER->id);
    if (!has_capability('tool/policy:managedocs', $personalcontext)) {
        $isManager = 0;
    }

    $response['status'] = true;
    $response['data']['nombre'] = $qromateca->nombre;
    $response['data']['user']   = $user->firstname . ' ' . $user->lastname;
    $response['data']['link']   = $qromateca->link;
    $response['data']['vistas'] = $qromateca->vistas;
    $response['data']['gestor'] = $isManager;
    $response['data']['creado'] = convertDateToSpanish(strtotime($qromateca->created_at));
    $response['data']['estado_aprobacion'] = $qromateca->estado_aprobacion;

    $documentoExt = $qromateca->documento_extension;

    $url = '//view.officeapps.live.com/op/embed.aspx?src=';
    if($documentoExt == 'pdf') {
       $url = 'https://docs.google.com/viewer?embedded=true&url=';
    }

    $response['data']['documento_url'] = $url . 'https://www.campusqroma.com/local/qromateca/docs/' . $qromateca->documento;

    return $response;
}

function guardarQromateca($details) {
    global $DB, $USER;

    $responseStatus = true;
    $nombre = $details['nombre'];
    $link = !empty($details['link']) ? $details['link'] : '';
    $type = $details['type'];

    $qromaFile = $_FILES['qromaFile'];
    $qromaFileDoc = $_FILES['qromaFileDoc'];

    $qromateca = new stdClass();
    $qromateca->nombre = $nombre;
    $qromateca->link = $link;
    $qromateca->tipo = $type;
    $qromateca->estado_aprobacion = 0;
    $qromateca->habilitado = 1;
    $qromateca->vistas = 0;
    $qromateca->id_usuario = $USER->id;
    $qromateca->created_at = date("Y-m-d H:i:s");

    if(!empty($qromaFile)) {
        $filename = urlencode($qromaFile['name']);
        $location = "qromateca/files/".$filename;
        $uploadOk = 1;
        $imageFileType = pathinfo($location,PATHINFO_EXTENSION);

        /* Valid Extensions */
        $valid_extensions = array("jpg","jpeg","png");
        /* Check file extension */
        if( !in_array(strtolower($imageFileType), $valid_extensions) ) {
            $uploadOk = 0;
        }

        if($uploadOk == 0){
            $responseStatus = false;
        }else{
            /* Upload file */
            $fullPathLocation = dirname(__DIR__, 2) .'/' . $location;
            if(!move_uploaded_file($qromaFile['tmp_name'], $fullPathLocation)){
                echo 0;
            }
        }
    }

    if(!empty($qromaFileDoc)) {
        $filenameDoc = urlencode($qromaFileDoc['name']);
        $location = "qromateca/docs/".$filenameDoc;
        $uploadOk = 1;
        $imageFileType = pathinfo($location,PATHINFO_EXTENSION);

        /* Valid Extensions */
        $valid_extensions = array("doc","docx","xls","xlsx","odt","pdf","ppt","pptx");
        /* Check file extension */
        if( !in_array(strtolower($imageFileType), $valid_extensions) ) {
            $uploadOk = 0;
        }

        if($uploadOk == 0){
            $responseStatus = false;
        }else{
            /* Upload file */
            $fullPathLocation = dirname(__DIR__, 2) .'/' . $location;
            if(!move_uploaded_file($qromaFileDoc['tmp_name'], $fullPathLocation)){
                echo 0;
            }
        }
    }

    $qromateca->imagen = $filename;
    $qromateca->documento = $filenameDoc;
    $qromateca->documento_extension = strtolower($imageFileType);

    $DB->insert_record('qromateca', $qromateca);

    $response['status'] = $responseStatus;

    return $response;
}

function actualizarVistas($id) {
    global $DB;

    $qromatecaObj = $DB->get_record_sql("SELECT * FROM {qromateca} WHERE id = ?", array($id));

    if (!empty($qromatecaObj)) {
        $qromatecaObj->vistas++;
        $qromatecaObj->updated_at = date("Y-m-d H:i:s");
        $DB->update_record('qromateca', $qromatecaObj);
    }

    $response['status'] = true;

    return $response;
}

function aprobar($id) {
    global $DB;

    $responseStatus = false;

    $qromatecaObj = $DB->get_record_sql("SELECT * FROM {qromateca} WHERE id = ?", array($id));

    if (!empty($qromatecaObj)) {
        $qromatecaObj->estado_aprobacion = 1;
        $qromatecaObj->updated_at = date("Y-m-d H:i:s");
        $DB->update_record('qromateca', $qromatecaObj);
        $responseStatus = true;
    }

    $response['status'] = $responseStatus;

    return $response;
}

function desaprobar($id) {
    global $DB;

    $qromatecaObj = $DB->get_record_sql("SELECT * FROM {qromateca} WHERE id = ?", array($id));

    if (!empty($qromatecaObj)) {
        $qromatecaObj->estado_aprobacion = 0;
        $qromatecaObj->updated_at = date("Y-m-d H:i:s");
        $DB->update_record('qromateca', $qromatecaObj);
    }

    $response['status'] = true;

    return $response;
}

function cargarComentarios($id) {
    global $DB, $USER;
    $data = $DB->get_records_sql("SELECT * FROM {qromateca_comentarios} WHERE id_qromateca = ? AND eliminado = 0", array($id));

    $comentarios = !empty($data) ? $data : array();

    foreach($comentarios as $comentario) {

        $user = $DB->get_record('user', array('id' => $comentario->id_usuario));

        $returnArr[] = [
            'id'=> $comentario->id,
            'comentario'=> $comentario->comentario,
            'user' => $user->firstname . ' ' . $user->lastname,
            'date' => 'Hace ' . timeSince(strtotime($comentario->created_at)),
            'comentario_user_id' => $user->id,
            'current_user_id' => $USER->id,
        ];
    }

    $response['status'] = true;
    $response['data'] = $returnArr;

    return $response;
}

function crearComentario($details) {
    global $DB, $USER;

    $qromatecaComentario = new stdClass();
    $qromatecaComentario->id_qromateca = $details['qromatecaId'];
    $qromatecaComentario->comentario = $details['comentTxt'];
    $qromatecaComentario->id_usuario = $USER->id;
    $qromatecaComentario->eliminado = 0;
    $qromatecaComentario->created_at = date("Y-m-d H:i:s");

    $DB->insert_record('qromateca_comentarios', $qromatecaComentario);

    $response['status'] = true;

    return $response;
}

function eliminarComentario($id) {
    global $DB;

    $comentarioObj = $DB->get_record_sql("SELECT * FROM {qromateca_comentarios} WHERE id = ?", array($id));

    if (!empty($comentarioObj)) {
        $comentarioObj->eliminado = 1;
        $comentarioObj->updated_at = date("Y-m-d H:i:s");
        $DB->update_record('qromateca_comentarios', $comentarioObj);
    }

    $response['status'] = true;

    return $response;
}