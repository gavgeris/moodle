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
?>

<!DOCTYPE html>
<html lang="el">
<head>
<meta charset="utf-8">
<title>Εργασίες προς επανυποβολή</title>
<link href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css" rel="stylesheet">
<style>
#results { margin-top: 20px; }
.loader { display: none; text-align: center; margin-top: 20px; }
</style>
</head>
<body>

<div class="container">
    <button id="loadButton" class="btn btn-warning">
        Δείξε τις εργασίες μου προς διόρθωση
    </button>

    <div class="loader">
        <img src="https://i.gifer.com/ZZ5H.gif" width="50" alt="Loading..."><br>
        Φόρτωση...
    </div>

    <div id="results"></div>
</div>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
$('#loadButton').on('click', function() {
    $('.loader').show();
    $('#results').html('');
    $.ajax({
        url: 'report_student_data.php',
        type: 'GET',
        data: { courseid: <?php echo $courseid; ?> },
        success: function(data) {
            $('.loader').hide();
            $('#results').html(data);
        },
        error: function() {
            $('.loader').hide();
            alert('Σφάλμα κατά τη φόρτωση των δεδομένων.');
        }
    });
});
</script>
</body>
</html>
