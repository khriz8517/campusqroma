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
 * A two column layout for the remui theme.
 *
 * @package   theme_remui
 * @copyright 2016 Damyon Wiese
 * @copyright (c) 2020 WisdmLabs (https://wisdmlabs.com/) <support@wisdmlabs.com>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

use core_course\external\course_summary_exporter;
use theme_remui\usercontroller;
use block_xp\local\xp\level_with_name;
use block_xp\local\xp\level_with_badge;

defined('MOODLE_INTERNAL') || die();

require_once('common.php');

function getUserImage() {
    global $USER;
    return new moodle_url('/user/pix.php/'.$USER->id.'/f1.jpg');
}

function convertDateToSpanish($timestamp) {
    setlocale(LC_TIME, 'es_ES', 'Spanish_Spain', 'Spanish');
    return strftime("%d de %B, %Y", $timestamp);
}

function getCourseImageById($courseId) {
    $course = get_course($courseId);
    return \theme_remui_coursehandler::get_course_image($course);
}

function getPendingCoursesHtml($courses) {
    global $CFG;
    $coursesHtml = '';

    foreach($courses as $key=>$course) {
        if($course->progress == 100) {
            continue;
        }

        $coursesHtml.= "<div class='item'>
                <div class='img'>
                   <div class='bg-img' style='background:url(". getCourseImageById($course->id) .")'></div>
                </div>
                <div class='contry'><img class='contry-flag' src='../local/qroma_front/img/pais/peru.jpg' alt='' srcset=''/>
                    <div class='contry-name'>PERÚ</div>
                </div>
                <h3 class='title gotham-medium'>".$course->fullname."</h3>
                <div class='footer'>
                    <a class='btn btn-info' href='../course/view.php?id={$course->id}'>ACCEDER</a>
                    <div class='progress'>
                        <div class='container-progress success'>
                            <div class='gauge-container'>
                                <svg class='gauge' viewBox='0 0 150 150'>
                                    <circle class='progress' r='67' data-target='84' cx='75' cy='75'></circle>
                                    <circle class='rail' r='67' data-target='84' cx='75' cy='75'></circle>
                                </svg><span class='center percentage'><span class='value'>". round($course->progress) ."</span><span class='percentSymbol'>%</span></span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>";

    }
    return $coursesHtml;
}

function getAllCoursesHtml($courses) {
    global $CFG;
    $coursesHtml = '';

    foreach($courses as $key=>$course) {

        $coursesHtml.= "<div class='item'>
            <div class='left'>
                <div class='photo'>
                    <div class='img' style='background:url(". getCourseImageById($course->id) ."')></div>
                </div>
                <div class='content'><a href='../course/view.php?id={$course->id}'>".$course->fullname."</a>
                <a class='text-success' href='../course/view.php?id={$course->id}'>ACCEDER</a></div>
            </div>
            <div class='right'>
                <div class='calendar'><img src='../local/qroma_front/img/icons/calendar.svg'/>
                    <div class='text'><span>Terminar el</span><br/><span>15 de Abril, 2021</span></div>
                </div>
                <div class='progress' style='box-shadow: 0 0 #FFFFFF;'>
                    <div class='container-progress success'>
                        <div class='gauge-container'>
                            <svg class='gauge' viewBox='0 0 150 150'>
                                <circle class='progress' r='67' data-target='84' cx='75' cy='75'></circle>
                                <circle class='rail' r='67' data-target='84' cx='75' cy='75'></circle>
                            </svg><span class='center percentage'><span class='value'>". round($course->progress) ."</span><span class='percentSymbol'>%</span></span>
                        </div>
                    </div>
                </div>
            </div>
        </div>";

    }
    return $coursesHtml;
}

$userCourses = array_values(usercontroller::get_users_courses_with_progress($USER));

global $USER, $CFG;
require_once($CFG->dirroot . '/user/profile/lib.php');

$colorcentro = false;
$qroma = true;

profile_load_custom_fields($USER);
$origen = $USER->profile['origen'];

if($origen == 'Color centro') {
    $colorcentro = true;
    $qroma = false;
}

$pendingCoursesExist = 0;

if(count($userCourses) > 0) {
    $pendingCoursesExist = 1;
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

$templatecontextDashboard = [
    'userpoints' => getUserLevel($userCourses, 1)['xp'],
    'levelimg' => getUserLevel($userCourses, 1)['img'],
    'levelimg2' => getUserLevel($userCourses, 2)['img'],
    'levelname' => getUserLevel($userCourses, 1)['levelName'],
    'userimg' => getUserImage(),
    'userfirstaccess' => convertDateToSpanish($USER->firstaccess),
    'username' => strtoupper($USER->firstname . ' ' . $USER->lastname),
    'pendingcourses' => getPendingCoursesHtml($userCourses),
    'pendingcoursesexist' => $pendingCoursesExist,
    'allcourses' => getAllCoursesHtml($userCourses),
    'colorcentro' => $colorcentro,
    'qroma' => $qroma
];

$templatecontext = array_merge($templatecontext, $templatecontextDashboard);

echo $OUTPUT->render_from_template('theme_remui/mydashboard', $templatecontext);
echo $OUTPUT->render_from_template('theme_remui/mydashboardqroma2', $templatecontext);