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
// Get the course ID
$parts = parse_url($_SERVER['HTTP_REFERER']);
parse_str($parts['query'], $query);
if ($query['id'] != "") {
	$courseid = $query['id'];
} else {
	$courseid = $_REQUEST["courseid"];
}
if (isset($_REQUEST['plus'])) {
    $isteacherplus = true;
    $sqlstmtfile = 'sql/sql_teacherplus.sql';
} else {
    $isteacherplus = false;
    $sqlstmtfile = 'sql/sql_teacher.sql';
}


require_login();
if (isguestuser()) {
    print_error('noguest');
}


$sqlstmt4 = file_get_contents($sqlstmtfile);


$result4= array();
// Fetch SQLStatement 4

if ($isteacherplus) {
    $rs = $DB->get_recordset_sql($sqlstmt4, array($courseid, $courseid));
} else {
    $rs = $DB->get_recordset_sql($sqlstmt4, array($courseid, $USER->id, $courseid, $courseid, $USER->id, $courseid));
}

//$rs = $DB->get_recordset_sql($sqlstmt4, array(2128, 5567, 2128, 2128, 5567, 2128));

foreach ($rs as $record) {
		array_push($result4, json_decode(json_encode($record), True));
}
$rs->close();

?>
<style>
.fdg_sortable {cursor:pointer;text-decoration:underline;color:#00f}
.alterRow {background-color:#dfdfdf}
</style>
<link href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-BVYiiSIFeK1dGmJRAkycuHAHRg32OmUcww7on3RYdg4Va+PmSTsz/K68vbdEjh4u" crossorigin="anonymous">

<?php
Fete_ViewControl_DataGrid::getInstance($result4)
->setGridAttributes(array('class' => 'table table-striped table-hover'))
->enableSorting(true)
->setup(array(
    'assignment' => array('header' => 'Εβδομάδα - Εργασία'),
    'plithos' => array('header' => 'Πλήθος'),
))
->setStartingCounter(1)
->setRowClass('row')
->render();
?>
