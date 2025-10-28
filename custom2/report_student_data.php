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

$sqlstmt5 = file_get_contents('sql/sql_student.sql');
$result5 = array();

$rs = $DB->get_recordset_sql($sqlstmt5, array($courseid, $USER->id));
foreach ($rs as $record) {
    $result5[] = json_decode(json_encode($record), true);
}
$rs->close();

echo '<link href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css" rel="stylesheet">';
echo '<style>.fdg_sortable{cursor:pointer;text-decoration:underline;color:#00f}.alterRow{background-color:#dfdfdf}</style>';

if (count($result5) > 0) {
    echo('<div class="alert alert-warning" style="text-align: justify;">
        <p>Οι ακόλουθες εργασίες έχουν λάβει ανατροφοδότηση από τον επιμορφωτή σας.
        Θα πρέπει να τις διορθώσετε και να τις ξαναυποβάλλετε προκειμένου να βαθμολογηθείτε.</p>
    </div>');

    Fete_ViewControl_DataGrid::getInstance($result5)
        ->setGridAttributes(['class' => 'table table-striped table-hover'])
        ->enableSorting(true)
        ->setup([
            'assignment' => ['header' => 'Εβδομάδα - Εργασία'],
            'plithos' => ['header' => 'Πλήθος'],
        ])
        ->setStartingCounter(1)
        ->setRowClass('row')
        ->render();
} else {
    echo '<div class="alert alert-success" style="text-align:center;">Δεν υπάρχουν εργασίες για επαναυποβολή.</div>';
}
