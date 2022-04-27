<?php
/**
 * Code fragment to define the version of the customcert module
 *
 * @package    mod_customcert
 * @copyright  2013 Mark Nelson <markn@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or late
 */

defined('MOODLE_INTERNAL') || die('Direct access to this script is forbidden.');

$plugin->version = 2020061500; // The current module version (Date: YYYYMMDDXX).
$plugin->requires = 2020061500; // Requires this Moodle version (3.9).
$plugin->cron = 0; // Period for cron to check this module (secs).
$plugin->component = 'mod_firma';

$plugin->maturity = MATURITY_STABLE;
$plugin->release = "3.9.0"; // User-friendly version number.
