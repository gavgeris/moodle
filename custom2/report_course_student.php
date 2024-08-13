<?php

/*
Εμφανίζει στον επιμορφούμενο της εργασιες που έχουν λάβει ανατροφοδότηση απο τν βαθμολογητή
και πρέπει να τις ξαναυποβάλλει. Εμφανίζεται ως μπλοκ στο μάθημα
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
// Get the course ID
$parts = parse_url($_SERVER['HTTP_REFERER']);
parse_str($parts['query'], $query);
if ($query['id'] != "") {
	$courseid = $query['id'];
} else {
	//$courseid = $_REQUEST["courseid"];
}


$sqlstmt5 = file_get_contents('sql/sql_student.sql');
$result5= array();

// Fetch SQLStatement 4
$rs = $DB->get_recordset_sql($sqlstmt5, array($courseid, $USER->id));
foreach ($rs as $record) {
	array_push($result5, json_decode(json_encode($record), True));
}
$rs->close();

?>
<style>
.fdg_sortable {cursor:pointer;text-decoration:underline;color:#00f}
.alterRow {background-color:#dfdfdf}
</style>
<link href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-BVYiiSIFeK1dGmJRAkycuHAHRg32OmUcww7on3RYdg4Va+PmSTsz/K68vbdEjh4u" crossorigin="anonymous">

<?php
if (count($result5) > 0) {
	echo('<div class="alert alert-warning" style="text-align: justify;">
  <p>Οι ακόλουθες εργασίες έχουν λάβει ανατροφοδότηση απο τον επιμορφωτή σας. Θα πρέπει να τις διορθώσετε και να τις ξαναυποβάλλετε προκειμένου να βαθμολογηθείτε.</p>
</div>');

	Fete_ViewControl_DataGrid::getInstance($result5)
	->setGridAttributes(array('class' => 'table table-striped table-hover'))
	->enableSorting(true)
	->setup(array(
	    'assignment' => array('header' => 'Εβδομάδα - Εργασία'),
	    'plithos' => array('header' => 'Πλήθος'),
	))
	->setStartingCounter(1)
	->setRowClass('row')
	->render();
}
?>
