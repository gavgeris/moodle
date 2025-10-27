<?php
define('NO_DEBUG_DISPLAY', true);
require_once('../config.php');
require_once('../lib/filelib.php');
require 'DataGrid.php';

require_login();
if (isguestuser()) {
    print_error('noguest');
}

global $DB, $USER;

$courseid = required_param('courseid', PARAM_INT);
$isteacherplus = optional_param('plus', 0, PARAM_BOOL);

if (is_siteadmin()) {
    $isteacherplus = true;
}

$sqlstmtfile = $isteacherplus ? 'sql/sql_teacherplus.sql' : 'sql/sql_teacher.sql';
$sqlstmt4 = file_get_contents($sqlstmtfile);

if ($isteacherplus) {
    $rs = $DB->get_recordset_sql($sqlstmt4, array($courseid, $courseid));
} else {
    $rs = $DB->get_recordset_sql($sqlstmt4, array($courseid, $USER->id, $courseid, $courseid, $USER->id, $courseid));
}

$result4 = [];
foreach ($rs as $record) {
    $result4[] = json_decode(json_encode($record), true);
}
$rs->close();

echo '<link href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css" rel="stylesheet">';
echo '<style>.fdg_sortable{cursor:pointer;text-decoration:underline;color:#00f}.alterRow{background-color:#dfdfdf}</style>';

Fete_ViewControl_DataGrid::getInstance($result4)
    ->setGridAttributes(['class' => 'table table-striped table-hover'])
    ->enableSorting(true)
    ->setup([
        'assignment' => ['header' => 'Εβδομάδα - Εργασία'],
        'plithos' => ['header' => 'Πλήθος'],
    ])
    ->setStartingCounter(1)
    ->setRowClass('row')
    ->render();
