<?php
// This file is part of Moodle - http://moodle.org/
//
// Moodle is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.
//
// Moodle is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with Moodle.  If not, see <http://www.gnu.org/licenses/>.

/**
 * Edwiser RemUI
 * @package   theme_remui
 * @copyright (c) 2020 WisdmLabs (https://wisdmlabs.com/) <support@wisdmlabs.com>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();
require_once('common.php');
require_once($CFG->libdir . "/badgeslib.php");

global $USER, $DB;

use theme_remui\usercontroller as usercontroller;
use block_xp\local\xp\level_with_name;
use block_xp\local\xp\level_with_badge;

function convertDateToSpanish($timestamp) {
    setlocale(LC_TIME, 'es_ES', 'Spanish_Spain', 'Spanish');
    return strftime("%d de %B, %Y", $timestamp);
}

function getUserImage() {
    global $USER;
    return new moodle_url('/user/pix.php/'.$USER->id.'/f1.jpg');
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

function getUserLevel($userCourses, $small) {
    global $USER;

    $world = \block_xp\di::get('course_world_factory')->get_world($userCourses[0]->id);
    $state = $world->get_store()->get_state($USER->id);
    $widget = new \block_xp\output\xp_widget($state, [], null, []);
    $level = $widget->state->get_level();

    //Get data
    $levelName = obtenerLevelPropertyValue($level, 'name');
    $xp = $widget->state->get_xp();

    $levelInfo = array('levelName' => $levelName, 'xp' =>$xp, 'img' => getLevelBadge($level, $small));

    return $levelInfo;
}

// Get user's object from page url.
$uid = optional_param('id', $USER->id, PARAM_INT);
$userobject = $DB->get_record('user', array('id' => $uid));

$context = context_user::instance($uid, MUST_EXIST);
if (user_can_view_profile($userobject, null, $context)) {
    $countries = get_string_manager()->get_list_of_countries();
    // Get the list of all country.
    if (!empty($userobject->country)) { // Country field in user object is empty.
        $temparray[] = array("keyName" => $userobject->country, "valName" => $countries[$userobject->country]);
        $temparray[] = array("keyName" => '', "valName" => 'Select a country...');
    } else {
        $temparray[] = array("keyName" => '', "valName" => 'Select a country...');
    }

    foreach ($countries as $key => $value) {
        $temparray[] = array("keyName" => $key, "valName" => $value);
    }

    $templatecontext['usercanmanage'] = \theme_remui\utility::check_user_admin_cap($userobject);
    $systemcontext = \context_system::instance();
    if ( has_capability('moodle/user:editownprofile', $systemcontext) ) {
        $templatecontext["haseditpermission"] = true;
    }
    $templatecontext['notcurrentuser'] = ($userobject->id != $USER->id) ? true : false;
    $templatecontext['countries'] = $temparray;

    // Prepare profile context.

    $hasinterests = false;
    $hasbadges = false;
    $onlypublic = true;
    $aboutme = false;
    $country = '';

    $userauth = get_auth_plugin($userobject->auth);
    $lockfields = array('field_lock_firstname', 'field_lock_lastname', 'field_lock_city', 'field_lock_country');
    foreach ($userauth->config as $key => $lockfield) {
        if ($lockfield == 'locked') {
            if (in_array($key, $lockfields)) {
                $userobject->$key = 'locked';
            }
        }
    }

    $templatecontext['user'] = $userobject;
    $templatecontext['user']->profilepicture = \theme_remui\utility::get_user_picture($userobject, 200);
    $templatecontext['user']->forumpostcount = usercontroller::get_user_forum_post_count($userobject);
    $templatecontext['user']->blogpostcount  = usercontroller::get_user_blog_post_count($userobject);
    $templatecontext['user']->contactscount  = usercontroller::get_user_contacts_count($userobject);
    $templatecontext['user']->description  = strip_tags($userobject->description);
    $templatecontext['user']->firstaccess  = convertDateToSpanish($userobject->firstaccess);
    $templatecontext['user']->qromaimage  = getUserImage();

    // About me tab data.
    $interests = \core_tag_tag::get_item_tags('core', 'user', $userobject->id);
    foreach ($interests as $interest) {
        $hasinterests = true;
        $aboutme = true;
        $templatecontext['user']->interests[] = $interest;
    }
    $templatecontext['user']->hasinterests    = $hasinterests;

    // Badges.
    if ($CFG->enablebadges) {
        if ($templatecontext['usercanmanage'] || ($userobject->id == $USER->id)) {
            $onlypublic = false;
        }
        $badges = badges_get_user_badges($userobject->id, 0, null, null, null, $onlypublic);
        if ($badges) {
            $hasbadges = true;
            $count = 0;
            foreach ($badges as $key => $badge) {
                $context = ($badge->type == BADGE_TYPE_SITE) ?
                context_system::instance() : context_course::instance($badge->courseid);
                $templatecontext['user']->badges[$count]['imageurl'] = moodle_url::make_pluginfile_url(
                    $context->id,
                    'badges',
                    'badgeimage',
                    $badge->id,
                    '/',
                    'f1',
                    false
                );
                $templatecontext['user']->badges[$count]['name'] = $badge->name;
                $templatecontext['user']->badges[$count]['link'] = new moodle_url('/badges/badge.php?hash=' . $badge->uniquehash);
                $templatecontext['user']->badges[$count]['desc'] = $badge->description;
                $count++;
            }
        }
    }
    $templatecontext['user']->hasbadges = $hasbadges;


    if (!empty($userobject->country)) {
        $country = get_string($userobject->country, 'countries');
    }
    $templatecontext['user']->location  = $userobject->address.$userobject->city.$country;
    $templatecontext['user']->instidept = $userobject->department.$userobject->institution;
    if (!empty($templatecontext['user']->location) || !empty($templatecontext['user']->instidept)) {
        $aboutme = true;
    }
    $templatecontext['user']->aboutme = $aboutme;

    // Courses tab data.
    $usercourses = array_values(usercontroller::get_users_courses_with_progress($userobject));

    foreach($usercourses as $key=>$usercourse) {
        if($usercourse->enddate == 0) {
            $usercourses[$key]->enddate = '<span>Fecha de finalización</span><br><span>no habilitada</span>';
        } else {
            $usercourses[$key]->enddate = '<span>Terminar el</span><br><span>'.convertDateToSpanish($usercourse->enddate).'</span>';
        }
    }

    $templatecontext['user']->hascourses = (count($usercourses)) ? true : false;
    $templatecontext['user']->courses = $usercourses;
    $templatecontext['user']->userpoints = getUserLevel($usercourses, 1)['xp'];
    $templatecontext['user']->userlevelbadge = getUserLevel($usercourses, 1)['img'];
}
echo $OUTPUT->render_from_template('theme_remui/mypublic', $templatecontext);

$PAGE->requires->strings_for_js(array(
    'enterfirstname',
    'enterlastname',
    'enteremailid',
    'enterproperemailid',
    'detailssavedsuccessfully',
    'actioncouldnotbeperformed'
), 'theme_remui');
