<?php
if (!defined('MOODLE_INTERNAL')) {
    die('Direct access to this script is forbidden.');    ///  It must be included from a Moodle page
}

require_once($CFG->dirroot.'/course/moodleform_mod.php');
require_once($CFG->dirroot.'/mod/firma/lib.php');

class mod_firma_mod_form extends moodleform_mod {

    function definition() {
        global $CFG, $DB, $OUTPUT, $PAGE;

        $PAGE->force_settings_menu();

        $mform =& $this->_form;

        $mform->addElement('header', 'generalhdr', get_string('general'));

        $mform->addElement('text', 'titulo', 'Título');
        $mform->addElement('text', 'descripcion', 'Descripción');

        $mform->addElement('text', 'nro_trabajadores', 'N° Trabajadores en el centro laboral');
        $mform->addElement('text', 'nro_registro', 'N° Registro');

        $options[1] = 'INDUCCIÓN';
        $options[2] = 'CAPACITACIÓN';
        $options[3] = 'ENTRENAMIENTO';
        $options[4] = 'SIMULACRO';

        $mform->addElement('select', 'tipo', 'Seleccione una opción', $options);
        $mform->addElement('text', 'hora_inicio', 'Hora inicio');
        $mform->addElement('text', 'hora_fin', 'Hora final');
        $mform->addElement('text', 'horas_total', 'N° Total de horas');
        $mform->addElement('text', 'capacitador', 'Apellidos y nombres del capacitador/entrenador');

        // Label does not add "Show description" checkbox meaning that 'intro' is always shown on the course page.
        $mform->addElement('hidden', 'courseId', $this->current->course);

        $mform->addElement('file', 'introfirma', 'Firma del capacitador');

        $this->standard_coursemodule_elements();

        $this->add_action_buttons();
    }

    /**
     * Allows module to modify the data returned by form get_data().
     * This method is also called in the bulk activity completion form.
     *
     * Only available on moodleform_mod.
     *
     * @param stdClass $data the form data to be modified.
     */
    public function data_postprocessing($data) {
        parent::data_postprocessing($data);
        // Turn off completion settings if the checkboxes aren't ticked.
        if (!empty($data->completionunlocked)) {
            $autocompletion = !empty($data->completion) && $data->completion == COMPLETION_TRACKING_AUTOMATIC;
            if (empty($data->completionattendanceenabled) || !$autocompletion) {
                $data->completionattendance = 0;
            }
        }
    }
}