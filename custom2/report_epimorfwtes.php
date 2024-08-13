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

require_login();
if (isguestuser()) {
    print_error('noguest');
}

$sqlstmt1 = file_get_contents('sql/sql_epimorfwtes_absent.sql'); // Αδιόρθωτες ανα επιμορφωτη

/*
$result = $DB->get_records_sql($sqlstmt, array($courseid, $USER->id));
$result2 = $DB->get_records_sql($sqlstmt2, array($courseid, $USER->id));
$result3 = $DB->get_records_sql($sqlstmt3);
*/

$result1= array();
$result2= array();
$result3= array();


// Fetch SQLStatement 1
$rs = $DB->get_recordset_sql($sqlstmt1, array($courseid, $USER->id, $courseid));
foreach ($rs as $record) {
	$row = json_decode(json_encode($record), True);
    $row2["warnresetbtn"] = getResetLink($row, 1);
	$row2["warn1btn"] = getWarnLink($row, 1);
	$row2["warn2btn"] = getWarnLink($row, 2);
	$row2["epimorfwths_plusbtn"] = getEpimorfwthsPlusLink($row, 3);
    $row2["resetepimorfwths_plusbtn"] = getResetEpimorfwthsPlusLink($row, 3);

	unset($row['courseid']);
	$row = array_merge ($row2, $row);
	array_push($result1, $row);
}
$rs->close();

function getWarnLink($row, $warn_type) {
	
	return '<a target="_blank" href="epimorfwtes_actions.php/?action=warn&email=' .$row["email"] . 
						'&epimorfwths_name=' .$row["epimorfwths"] .
						'&days=' .$row["days"] .
						'&plithos=' .$row["plithos"] .
						'&courseid=' .$row["courseid"] .
						'&course=' .$row["fullname"] .
						'&warn_type=' .$warn_type . '">' .
                        '<img src="./files/warn' . $warn_type . '.png" width=32px;/>'.
                         '</a><br>' .
						getWarn($row["email"], $row["courseid"], $warn_type) ;
}
function getResetLink($row, $warn_type) {
    return '<a target="_blank" href="epimorfwtes_actions.php/?action=resetwarn&email=' .$row["email"] .
        '&epimorfwths_name=' .$row["epimorfwths"] .
        '&days=' .$row["days"] .
        '&plithos=' .$row["plithos"] .
        '&courseid=' .$row["courseid"] .
        '&course=' .$row["fullname"] .
        '&warn_type=' .$warn_type . '">' .
        '<img src="https://img.icons8.com/android/24/000000/recurring-appointment.png"/></a>';
}

function getEpimorfwthsPlusLink($row, $warn_type) {
	return '<a target="_blank" href="epimorfwtes_actions.php/?action=epimorfwths_plus' .
                        '&email=' .$row["email"] .
						'&courseid=' .$row["courseid"] .
						'&course=' .$row["fullname"] .
						'&groupname=' . $row["name"] .
                        '&warn_type=' .$warn_type .
						'"><img style="text-align:center;" src="./files/epimorfwths_plus.png" width=32px;/></a><br>' .
                        getEpimorfwthplus($row["email"], $row["courseid"], $warn_type) ;
}

function getResetEpimorfwthsPlusLink($row, $warn_type) {
    return '<a target="_blank" href="epimorfwtes_actions.php/?action=resetepimorfwth_plus' .
        '&email=' .$row["email"] .
        '&courseid=' .$row["courseid"] .
        '&course=' .$row["fullname"] .
        '&groupname=' . $row["name"] .
        '&warn_type=' .$warn_type .
        '"><img src="https://img.icons8.com/android/24/000000/recurring-appointment.png"/></a><br>';
}

function getWarn($email, $courseid, $warn_type) {
	global $DB;
	$sql = "SELECT DATEDIFF(NOW(), MAX(FROM_UNIXTIME(warn_time))) FROM mdl_epimorfwtes_warnings WHERE epimorfwths = ? and courseid = ? and warn_type = ?";
	return $DB->get_field_sql($sql, [$email, $courseid, $warn_type]);
}

function getEpimorfwthplus($email, $courseid, $warn_type) {
    global $DB;
    $sql = "SELECT DATEDIFF(NOW(), MAX(FROM_UNIXTIME(warn_time))) FROM mdl_epimorfwtes_warnings WHERE epimorfwths = ? and courseid = ? and warn_type = ?";
    return $DB->get_field_sql($sql, [$email, $courseid, $warn_type]);
}

?>
<style>
.fdg_sortable {cursor:pointer;text-decoration:underline;color:#00f}
.alterRow {background-color:#dfdfdf}
</style>
<script
			  src="https://code.jquery.com/jquery-3.4.1.min.js"
			  integrity="sha256-CSXorXvZcTkaix6Yvo6HppcZGetbYMGWSFlBw8HfCJo="
			  crossorigin="anonymous"></script>

<link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/v/dt/jszip-2.5.0/dt-1.10.22/b-1.6.5/b-html5-1.6.5/datatables.min.css"/>
 
<script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.36/pdfmake.min.js"></script>
<script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.36/vfs_fonts.js"></script>
<script type="text/javascript" src="https://cdn.datatables.net/v/dt/jszip-2.5.0/dt-1.10.22/b-1.6.5/b-html5-1.6.5/datatables.min.js"></script>
<script>

jQuery(document).ready(function () {
/*	var tf = new TableFilter(document.querySelector('.mytable'), {
		base_path: '/custom2/tablefilter/',
		 auto_filter: {
            delay: 500 //milliseconds
        },
		alternate_rows: true,
        btn_reset: true,
		col_types: [
        'string',
        'string',
        'string',
        'string',
        'string',
        'number',
        'number',
        'number',
        'number',
        'number',
        'number',
        'number',
        'number',
        'number',
        'number',
        'number',
        'number',
        'number',
        'number',
        'number',
        'number',
        'number',
        'number',
        'number',
        'number',
        'number',
        'number',
        'number',
        'number',
        'string',
        'string',
        'string'
		],
		extensions: [{
            name: 'sort'
        }]
	});
	tf.init();
	*/
	/*
	 // Setup - add a text input to each footer cell
    $('.mytable thead th').each( function () {
        var title = $(this).text();
        $(this).html( title+'<input type="text" placeholder=" '+title+'" />' );
    } );
	*/
    $('.mytable').DataTable({
		 dom: 'Bfrtip',
		 select: true,
		 "pageLength": 50,
		buttons: [
			'copy', 
			{
                extend: 'excelHtml5',
                title: 'Epimorfwtes'
            }, 'pdf'
		],
	} );
});

/*
		initComplete: function () {
            // Apply the search
            this.api().columns().every( function () {
                var that = this;
 
                $( 'input', this.header() ).on( 'keyup change clear', function () {
                    if ( that.search() !== this.value ) {
                        that
                            .search( this.value )
                            .draw();
                    }
                } );
            } );
        }
		*/
</script>
<link href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-BVYiiSIFeK1dGmJRAkycuHAHRg32OmUcww7on3RYdg4Va+PmSTsz/K68vbdEjh4u" crossorigin="anonymous">
<h2>Εκκρεμείς εργασίες ανα επιμορφωτή</h2>
<script>
  function sendemail(email, plithos) {
	  alert(email);
	  
  }
 </script>
<?php
Fete_ViewControl_DataGrid::getInstance($result1)
->setGridAttributes(array('class' => 'mytable table-striped table-hover'))
->enableSorting(false)
->setup(array(
    'warn1btn' => array('header' => 'Strike 1'),
    'warnresetbtn' => array('header' => 'Reset Strikes'),
    'warn1' => array('header' => '1η ειδοποίηση'),
    'warn2btn' => array('header' => 'Strike 2'),
    'resetepimorfwths_plusbtn' => array('header' => 'Reset Επιμορφωτή+'),
    'warn2' => array('header' => '2η ειδοποίηση'),
    'epimorfwths_plusbtn' => array('header' => 'Επιμορφωτής+'),
    'epimorfwths' => array('header' => 'Επιμορφωτής'),
    'weekno' => array('header' => 'Εβδομάδα'),
    'plithos' => array('header' => 'Πλήθος'),
    'days' => array('header' => 'Ημέρες Απουσίας'),
    'fullname' => array('header' => 'Σεμινάριο'),
    'name' => array('header' => 'Ομάδα'),
    'syntonisths' => array('header' => 'Συντονιστής'),
    'email' => array('header' => 'email'),
    'week0' => array('header' => 'Ε0	'),
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
    'week17' => array('header' => 'Ε17'),
    'week18' => array('header' => 'Ε18'),
    'week19' => array('header' => 'Ε19'),
    'week20' => array('header' => 'Ε20'),
    'week21' => array('header' => 'Ε21'),
    'week22' => array('header' => 'Ε22')
))
->setStartingCounter(1)
->setRowClass('row')
->render();
?>
