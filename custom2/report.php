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

$sqlstmt1 = file_get_contents('sql/sql1.sql'); // Αναλυτικά αδιόρθωτες εργασίες
$sqlstmt2 = file_get_contents('sql/sql2.sql'); // Εκκρεμείς εργασίες ανα επιμορφωτή
$sqlstmt3 = file_get_contents('sql/sql3.sql'); // Ημέρες απουσίας

/*
$result = $DB->get_records_sql($sqlstmt, array($courseid, $USER->id));
$result2 = $DB->get_records_sql($sqlstmt2, array($courseid, $USER->id));
$result3 = $DB->get_records_sql($sqlstmt3);
*/

$result1= array();
$result2= array();
$result3= array();


// Fetch SQLStatement 1
//$rs = $DB->get_recordset_sql($sqlstmt1, array($courseid, $USER->id, $courseid));
//foreach ($rs as $record) {
//	array_push($result1, json_decode(json_encode($record), True));
//}
//$rs->close();
// Fetch SQLStatement 2
$rs = $DB->get_recordset_sql($sqlstmt2, array($courseid, $USER->id, $courseid, $USER->id));
foreach ($rs as $record) {
	array_push($result2, json_decode(json_encode($record), True));
}
$rs->close();
// Fetch SQLStatement 3
$rs = $DB->get_recordset_sql($sqlstmt3, array($courseid, $USER->id));
foreach ($rs as $record) {
	array_push($result3, json_decode(json_encode($record), True));
}
$rs->close();

/*
$PAGE->set_heading("heading");
// Output starts here.
$PAGE->navbar->add("");
$PAGE->set_title("Αναφορά επιμορφωτή");
$PAGE->set_heading("Αναφορά επιμορφωτή");
$PAGE->set_pagelayout('incourse');

echo $OUTPUT->header();
echo $OUTPUT->heading("Αναφορά επιμορφωτή");
*/
?>
<style>
.fdg_sortable {cursor:pointer;text-decoration:underline;color:#00f}
.alterRow {background-color:#dfdfdf}
</style>
<link href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-BVYiiSIFeK1dGmJRAkycuHAHRg32OmUcww7on3RYdg4Va+PmSTsz/K68vbdEjh4u" crossorigin="anonymous">
<h2>Εκκρεμείς εργασίες ανα επιμορφωτή</h2>

<?php
Fete_ViewControl_DataGrid::getInstance($result2)
->setGridAttributes(array('class' => 'ekkremeis table table-striped table-hover'))
->enableSorting(true)
->setup(array(
    'epimorfwths' => array('header' => 'Επιμορφωτής'),
    'weekno' => array('header' => 'Εβδομάδα'),
    'plithos' => array('header' => 'Πλήθος'),
    'days' => array('header' => 'Ημέρες Απουσίας'),
    'fullname' => array('header' => 'Σεμινάριο'),
    'name' => array('header' => 'Ομάδα'),
    'email' => array('header' => 'email'),
 'WEEK1' => array('header' => 'Ε1'),
    'WEEK2' => array('header' => 'Ε2'),
    'WEEK3' => array('header' => 'Ε3'),
    'WEEK4' => array('header' => 'Ε4'),
    'WEEK5' => array('header' => 'Ε5'),
    'WEEK6' => array('header' => 'Ε6'),
    'WEEK7' => array('header' => 'Ε7'),
    'WEEK8' => array('header' => 'Ε8'),
    'WEEK9' => array('header' => 'Ε9'),
    'WEEK10' => array('header' => 'Ε10'),
    'WEEK11' => array('header' => 'Ε11'),
    'WEEK12' => array('header' => 'Ε12'),
    'WEEK13' => array('header' => 'Ε13'),
    'WEEK14' => array('header' => 'Ε14'),
    'WEEK15' => array('header' => 'Ε15'),
    'WEEK16' => array('header' => 'Ε16'),
    'WEEK17' => array('header' => 'Ε17'),
    'WEEK18' => array('header' => 'Ε18'),
    'WEEK19' => array('header' => 'Ε19'),
    'WEEK20' => array('header' => 'Ε20'),
    'WEEK21' => array('header' => 'Ε21'),
    'WEEK22' => array('header' => 'Ε22'),
    'WEEK23' => array('header' => 'Ε23'),
    'WEEK24' => array('header' => 'Ε24'),
    'WEEK25' => array('header' => 'Ε25'),
    'WEEK26' => array('header' => 'Ε26'),
    'WEEK27' => array('header' => 'Ε27'),
    'WEEK28' => array('header' => 'Ε28'),
    'WEEK29' => array('header' => 'Ε29'),
    'WEEK30' => array('header' => 'Ε30'),
	'courseid' => array('header' => 'ID')
))
->setStartingCounter(1)
->setRowClass('row')
->render();
?>
<h2>Ημέρες απουσίας</h2>
<?php
Fete_ViewControl_DataGrid::getInstance($result3)
->setGridAttributes(array('class' => 'absent_days table table-striped table-hover'))
->enableSorting(true)
->setup(array(
    'days' => array('header' => 'Ημέρες'),
    'fullname' => array('header' => 'Επιμορφωτής'),
    'email' => array('header' => 'email'),
    'coursename' => array('header' => 'Σεμινάριο'),
    'groupname' => array('header' => 'Ομάδα')
))
->setStartingCounter(1)
->setRowClass('row')
->render();
?>
<h2>Αναλυτικά</h2>
<?php
/*
Fete_ViewControl_DataGrid::getInstance($result1)
->setGridAttributes(array('class' => 'details table table-striped table-hover'))
->enableSorting(true)
->setup(array(
    'coursename' => array('header' => 'Σεμινάριο'),
    'name' => array('header' => 'Ομάδα'),
    'assignment' => array('header' => 'Εβδομάδα - Εργασία'),
    'days' => array('header' => 'Ημέρες απο την υποβολή'),
    'days2' => array('header' => 'Ημέρες απο τη λήξη της προθεσμίας'),
    'epimorfwths' => array('header' => 'Επιμορφωτής'),
    'email' => array('header' => 'email'),
    'epimorfoumenos' => array('header' => 'Επιμορφούμενος')
))
->setStartingCounter(1)
->setRowClass('row')
->render();
*/
?>

<link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/v/dt/jszip-2.5.0/dt-1.10.22/b-1.6.5/b-html5-1.6.5/datatables.min.css"/>
 <script src="https://code.jquery.com/jquery-3.4.1.min.js"></script>
<script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.36/pdfmake.min.js"></script>
<script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.36/vfs_fonts.js"></script>
<script type="text/javascript" src="https://cdn.datatables.net/v/dt/jszip-2.5.0/dt-1.10.22/b-1.6.5/b-html5-1.6.5/datatables.min.js"></script>
<script>
$('.ekkremeis, .details, .absent_days').DataTable({
		 dom: 'Bfrtip',
		 select: true,
		 "pageLength": 50,
		buttons: [
			'copy', 
			{
                extend: 'excelHtml5',
                title: 'data'
            }
		],
	} );
</script>
