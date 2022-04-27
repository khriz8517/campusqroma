<?php

namespace local_tempmigrationqroma\task;

/**
 * A scheduled task class for CAS user sync.
 *
 * @copyright  2015 Vadim Dvorovenko <Vadimon@mail.ru>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class tempmigrationqroma extends \core\task\scheduled_task
{

    /**
     * Get a descriptive name for this task (shown to admins).
     *
     * @return string
     */
    public function get_name()
    {
        return 'Migrate users to QROMA - seguimiento';
    }

    /**
     * Run users sync.
     */
    public function execute()
    {
        global $CFG;
        require_once($CFG->dirroot . '/local/tempmigrationqroma/lib.php');
        tempmigrationqroma_task();
    }

}