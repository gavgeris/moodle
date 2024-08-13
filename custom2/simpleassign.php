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
 * This file is the entry point to the assign module. All pages are rendered from here
 *
 * @package   mod_assign
 * @copyright 2012 NetSpot {@link http://www.netspot.com.au}
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require_once('../config.php');
require_once($CFG->dirroot . '/mod/assign/locallib.php');
require_once(dirname(__FILE__).'/phpQuery-onefile.php');

//$id = required_param('id', PARAM_INT);
$USER = $DB->get_record('user', array('id'=>'6')); // This user is enrolled on all courses

list ($course, $cm) = get_course_and_cm_from_cmid($id, 'assign');

$context = context_module::instance($cm->id);

$assign = new assign($context, $cm, $course);
// Get the assign class to
// render the page.
$page =  $assign->view();
// $page = str_replace('<img src="http://', '<img src="http://seminars.etwinning.gr/custom/getimage.php?image=http://', $page);
//echo $page;
phpQuery::newDocumentHTML ($page);
echo '<link rel="stylesheet" type="text/css" href="https://seminars.etwinning.gr/theme/styles.php/aardvark/1543860266_1/all">';
echo('<h2 style="text-align:center">' .$course->fullname . '</h2>');
echo('<h3 style="text-align:center"><b>' .pq('div h2:first')->html(). '</b></h3>');


echo(pq('#intro')->html());

