<?php

namespace LocalPages\Controller;

require_once realpath(dirname(__FILE__)) . '/../model/QromaData.php';

use core_course_category;
use LocalPages\QromaData as QromaDataModel;

const NO_SUBCATEGORY = 1;
const ONE_SUBCATEGORY = 2;
const TWO_SUBCATEGORY = 3;

class QromaData {
    public function __construct() {
    }

    private function getQromaDataModel() {
        return new QromaDataModel();
    }

    public function ObtenerCursos() {
        return $this->getQromaDataModel()->ObtenerCursos();
    }

    public function ObtenerCantidadCursos() {
        return count($this->getQromaDataModel()->ObtenerCursos());
    }

    public function ObtenerTituloCursos() {
        $return = array();

        $cantidadCursos = $this->ObtenerCantidadCursos();

        $labelCurso = 'Cursos';
        $labelDisponible = 'disponibles';

        if($cantidadCursos == 1) {
            $labelCurso = 'Curso';
            $labelDisponible = 'disponible';
        }

        $return['cursosLabel'] = $cantidadCursos . ' ' . $labelCurso;
        $return['disponible'] = $labelDisponible;

        return $return;
    }

    public function obtenerSubcategorias($origen) {
        $multiple = false;
        $category = core_course_category::get($origen);
        $subcategorias = $category->get_children();

        if(empty($subcategorias)) {
            $response['type'] = NO_SUBCATEGORY;
        } else {
            foreach($subcategorias as $subcategory) {
                if(!empty($subcategory->get_children())) {
                    $multiple = true;
                }
                $subcategoriasData[] = ['name' => $subcategory->name, 'status' => $subcategory->visible];
            }
            $response['type'] = ONE_SUBCATEGORY;
            if($multiple) {
                $response['type'] = TWO_SUBCATEGORY;
            }
            $response['data'] = $subcategoriasData;
        }

        return $response;
    }

    public function getUserType() {
        global $USER;
        profile_load_custom_fields($USER);
        return $USER;
    }
}