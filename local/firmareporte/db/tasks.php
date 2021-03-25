<?php

defined('MOODLE_INTERNAL') || die();

$tasks = array(
    array(
        'classname' => 'local_firmareporte\task\firmareporte',
        'blocking' => 0,
        'minute' => '30',
        'hour' => '17',
        'day' => '*',
        'month' => '1,7',
        'dayofweek' => '0'
    )
);