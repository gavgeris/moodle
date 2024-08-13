<head>
<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0-beta.2/css/bootstrap.min.css" integrity="sha384-PsH8R72JQ3SOdhVi3uxftmaW6Vc51MKb0q5P2rRUpPvrszuE4W1povHYgTpBfshb" crossorigin="anonymous">
</head>
<body>

<?php
/*
Αυτο το script στέλνει ένα test email με συγκεκριμένο subject στον επιμορφούμενο
που είναι συνδεδεμένος ($USER). Χρησιμοποιείτε στο σεμινάριο V, στην ενότητα του phishing - spam

*/
// disable moodle specific debug messages and any errors in output
define('NO_DEBUG_DISPLAY', true);

require_once('../config.php');
require_once('../lib/filelib.php');
require("class.phpmailer.php");

require_login();
if (isguestuser()) {
    print_error('noguest');
	die(0);
}

$result= array();

// Fetch SQLStatement 4
$rs = $DB->get_recordset_sql("SELECT cg.course_title, finaltmima, emailpsd, email, surname, name FROM epimorfwtes e, course_groups cg
WHERE finaltmima IS NOT NULL AND accepted=1
  AND finalseminar = cg.course;");
foreach ($rs as $record) {
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

	$mail->Subject = "[Σεμινάρια etwinning] - Επιλογή επιμορφωτή και Ανάθεση τμήματος - Ορθή επανάληψη";

	//echo $record->emailpsd;
	$mail->AddAddress($record->emailpsd, $record->surname . " " . $record->name);
	if ($record->email != $record->emailpsd) 
		$mail->AddAddress($record->email, $record->surname . " " . $record->name);

	$message = "<p>Καλησπέρα σας, λαμβάνετε αυτό το email γιατί έχετε επιλεχθεί ως επιμορφωτής στα σεμινάρια etwinning. Σας ενημερώνουμε πως έχετε επιλεγεί να είστε επιμορφωτής στο σεμινάριο</p>
				<h2 style='text-align:center;'>" .  $record->course_title . "</h2><h2 style='text-align:center;'> " . $record->finaltmima . "</h2><br>
				<p>Η επιλογή έγινε με βάση τις προτιμήσεις σας αλλά και τις ανάγκες των σεμιναρίων</p>
				<p>Θα ακολουθήσει ανακοίνωση σχετικα με τον τρόπο πρόσβασης και την έναρξη των σεμιναρίων</p>
				<p><br>Η ομάδα διαχείρισης των σεμιναρίων<p>";
	$mail->Body    = $message;
	$mail->AltBody = $message;
	$mail->Send();
	echo("Στάλθηκε στα email:" . $record->emailpsd . " " . $record->email . "<br>");
	$mail = null;
	sleep(0.5);
}
$rs->close();


//

?>

 </body>
