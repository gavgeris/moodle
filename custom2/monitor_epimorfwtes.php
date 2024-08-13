<?php

define('NO_DEBUG_DISPLAY', true);

require_once('../config.php');
require_once('../lib/filelib.php');
require 'DataGrid.php';

global $DB;

require_login();
if (isguestuser()) {
    print_error('noguest');
}

$sqlstmt1 = file_get_contents('sql/sql_monitor_epimorfwtes.sql'); 

$ABSENT_DAYS_LIMIT = 5; 
$UNGRADED_LIMIT_PER_WEEK = 5;
$UNGRADED_LIMIT_SUM = 20;
$EPIMORFWTHS_PLUS_LIMIT = 30;

$REASON_DAYS = 0;
$REASON_LIMIT_WEEK = 1;
$REASON_LIMIT_SUM = 2;
$REASON_LIMIT_EPIMORFWTH_PLUS = 3;



// Fetch SQLStatement 1
$rs = $DB->get_recordset_sql($sqlstmt1, array($courseid, $USER->id, $courseid));
foreach ($rs as $record) {
	array_push($result1, json_decode(json_encode($record), True));
	echo $record->epimorfwths . '<br>';
	$prop = 'week' . $record->cur_week;
	
	if ($record->plithos >= $EPIMORFWTHS_PLUS_LIMIT) {
		sendmail($record->epimorfwths, $record->email, $REASON_DAYS, $record );
	} else if ($record->plithos >= $UNGRADED_LIMIT_SUM) {
		sendmail($record->epimorfwths, $record->email, $REASON_LIMIT_SUM, $record );
	} else if ($record->{$prop} >= $UNGRADED_LIMIT_PER_WEEK) {
		sendmail($record->epimorfwths, $record->email, $REASON_LIMIT_WEEK, $record );
	}
}

function sendmail($name, $email, $reason, $record) {
	echo $name . ' ' .  $email . ' ' . $reason;
	
	$mail = new PHPMailer();
	$mail->CharSet = "UTF-8";
	$mail->IsSMTP();

	$mail->Host = "mail.sch.gr";  // specify main and backup server
	$mail->SMTPAuth = true;     // turn on SMTP authentication
	$mail->Username = "admsup";  // SMTP username
	$mail->Password = "Xc4MbY5U"; // SMTP password

	$mail->From = "admin@seminars.etwinning.gr";
	$mail->FromName = 'Διαχειριστής σεμιναρίων etwinning';
	$mail->WordWrap = 50;
	$mail->IsHTML(true);

	$mail->Subject = "[Σεμινάρια etwinning] Εκκρεμείς εργασίες";

	//echo $record->emailpsd;
	$mail->AddAddress($email, $name);
	//$mail->AddAddress($record->email, $record->surname . " " . $record->name);

	$message = "<p>Καλησπέρα σας, λαμβάνετε αυτό το email γιατί έχετε επιλεχθεί ως επιμορφωτής στα σεμινάρια etwinning. Σας ενημερώνουμε πως έχετε επιλεγεί να είστε επιμορφωτής στο σεμινάριο</p>
				<h2 style='text-align:center;'>" .  $record->course_title . "</h2><h2 style='text-align:center;'> " . $record->finaltmima . "</h2><br>
				<p>Η επιλογή έγινε με βάση τις προτιμήσεις σας αλλά και τις ανάγκες των σεμιναρίων</p>
				<p>Θα ακολουθήσει ανακοίνωση σχετικα με τον τρόπο πρόσβασης και την έναρξη των σεμιναρίων</p>
				<p><br>Η ομάδα διαχείρισης των σεμιναρίων<p>";
	$mail->Body    = $message;
	$mail->AltBody = $message;
	//$mail->Send();
	echo("Στάλθηκε στα email:" . $record->emailpsd . " " . $record->email . "<br>");
	$mail = null;	
	
}

$rs->close();
?>
<style>
.fdg_sortable {cursor:pointer;text-decoration:underline;color:#00f}
.alterRow {background-color:#dfdfdf}
</style>
<link href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-BVYiiSIFeK1dGmJRAkycuHAHRg32OmUcww7on3RYdg4Va+PmSTsz/K68vbdEjh4u" crossorigin="anonymous">
<h2>Εκκρεμείς εργασίες ανα επιμορφωτή</h2>
<script>
  function sendemail(email, plithos) {
	  alert(email);
	  
  }
 </script>
<?php
Fete_ViewControl_DataGrid::getInstance($result1)
->setGridAttributes(array('class' => 'table table-striped table-hover'))
->enableSorting(true)
->setup(array(
    'emailbtn' => array('header' => 'Email'),
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
    'week17' => array('header' => 'Ε17'),
    'week18' => array('header' => 'Ε18'),
    'week19' => array('header' => 'Ε19')
))
->setStartingCounter(1)
->setRowClass('row')
->render();
?>
