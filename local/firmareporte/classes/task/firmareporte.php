<?php

namespace local_firmareporte\task;

/**
 * A scheduled task class for CAS user sync.
 *
 * @copyright  2015 Vadim Dvorovenko <Vadimon@mail.ru>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class firmareporte extends \core\task\scheduled_task
{

    /**
     * Get a descriptive name for this task (shown to admins).
     *
     * @return string
     */
    public function get_name(){
        return 'QROMA - Guardar firmas del dia';
    }

    /**
     * Run users sync.
     */
    public function execute(){
        global $CFG;
        require_once($CFG->dirroot . '/local/firmareporte/lib.php');
        firmareporte_task();
    }

}