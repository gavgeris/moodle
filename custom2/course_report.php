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
 * This script serves draft files of current user
 *
 * @package    core
 * @subpackage file
 * @copyright  2008 Petr Skoda (http://skodak.org)
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

// disable moodle specific debug messages and any errors in output
define('NO_DEBUG_DISPLAY', true);

require_once('../config.php');
require_once('../lib/filelib.php');
require 'DataGrid.php';

global $DB;
$courseid = $_REQUEST["courseid"];

require_login();
if (isguestuser()) {
    print_error('noguest');
}

$sqlstmt1 = file_get_contents('sql/sql_course_report.sql'); // Αδιόρθωτες ανα επιμορφωτη

/*
$result = $DB->get_records_sql($sqlstmt, array($courseid, $USER->id));
$result2 = $DB->get_records_sql($sqlstmt2, array($courseid, $USER->id));
$result3 = $DB->get_records_sql($sqlstmt3);
*/

$result1= array();
$result2= array();
$result3= array();


// Fetch SQLStatement 1
$rs = $DB->get_recordset_sql($sqlstmt1, array($courseid));
foreach ($rs as $record) {
	array_push($result1, json_decode(json_encode($record), True));
}
$rs->close();
?>
<style>
.fdg_sortable {cursor:pointer;text-decoration:underline;color:#00f}
.alterRow {background-color:#dfdfdf}
</style>
<link href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-BVYiiSIFeK1dGmJRAkycuHAHRg32OmUcww7on3RYdg4Va+PmSTsz/K68vbdEjh4u" crossorigin="anonymous">
<h2>Εκκρεμείς εργασίες ανα επιμορφωτή</h2>

<?php
Fete_ViewControl_DataGrid::getInstance($result1)
->setGridAttributes(array('class' => 'table table-striped table-hover'))
->enableSorting(true)
->setup(array(
    'epimorfwths' => array('header' => 'Επιμορφωτής'),
    'weekno' => array('header' => 'Εβδομάδα'),
    'plithos' => array('header' => 'Πλήθος'),
    'days' => array('header' => 'Ημέρες Απουσίας'),
    'fullname' => array('header' => 'Σεμινάριο'),
    'name' => array('header' => 'Ομάδα'),
    'syntonisths' => array('header' => 'Συντονιστής'),
    'email' => array('header' => 'email'),
    'week1' => array('header' => 'Ε1'),
    'week2' => array('header' => 'Ε2'),
    'week3' => array('header' => 'Ε3'),
    'week4' => array('header' => 'Ε4'),
    'week5' => array('header' => 'Ε5'),
    'week6' => array('header' => 'Ε6'),
    'week7' => array('header' => 'Ε7'),
    'week8' => array('header' => 'Ε8'),
    'week9' => array('header' => 'Ε9'),
    'week10' => array('header' => 'Ε10'),
    'week11' => array('header' => 'Ε11'),
    'week12' => array('header' => 'Ε12'),
    'week13' => array('header' => 'Ε13'),
    'week14' => array('header' => 'Ε14'),
    'week15' => array('header' => 'Ε15'),
    'week16' => array('header' => 'Ε16'),
    'week17' => array('header' => 'Ε17')
))
->setStartingCounter(1)
->setRowClass('row')
->render();
?>
