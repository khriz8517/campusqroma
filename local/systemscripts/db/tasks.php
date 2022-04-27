<?php

/**
 * Definition of auth_cas tasks.
 *
 * @package    auth_cas
 * @category   task
 * @copyright  2015 Vadim Dvorovenko <Vadimon@mail.ru>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

$tasks = array(
    array(
        'classname' => 'local_systemscripts\task\migrate_qroma_users',
        'blocking' => 0,
        'minute' => '30',
        'hour' => '17',
        'day' => '*',
        'month' => '1,7',
        'dayofweek' => '0'
    )
);