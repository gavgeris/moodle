<?php
define('NO_DEBUG_DISPLAY', true);
require_once('../config.php');
require_login();
if (isguestuser()) {
    print_error('noguest');
}
$courseid = required_param('courseid', PARAM_INT);
?>
<!DOCTYPE html>
<html lang="el">
<head>
    <meta charset="utf-8">
    <title>Αδιορθωτές εργασίες</title>
    <link href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css" rel="stylesheet">
    <style>
        #results {
            margin-top: 20px;
        }
        .loader {
            display: none;
            text-align: center;
            margin-top: 20px;
        }
    </style>
</head>
<body>

<div class="container">
    <button id="loadButton" class="btn btn-primary">
        Δείξε μου τι έχω για διόρθωση
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
            url: 'report_course_data.php',
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
