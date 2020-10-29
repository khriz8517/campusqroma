<?php  // Moodle configuration file

unset($CFG);
global $CFG;
$CFG = new stdClass();

$CFG->dbtype    = 'sqlsrv';
$CFG->dblibrary = 'native';
$CFG->dbhost    = 'moodle-test.database.windows.net';
$CFG->dbname    = 'moodle';
$CFG->dbuser    = 'admlocal';
$CFG->dbpass    = 'Cppq$2020qroma';
$CFG->prefix    = 'mdl_';
$CFG->dboptions = array (
  'dbpersist' => 0,
  'dbport' => '',
  'dbsocket' => '',
);

$CFG->wwwroot   = 'https://www.campusqroma.com';
$CFG->dataroot  = 'F:\\moodledata';
$CFG->admin     = 'admin';

$CFG->directorypermissions = 0777;

require_once(__DIR__ . '/lib/setup.php');

// There is no php closing tag in this file,
// it is intentional because it prevents trailing whitespace problems!
