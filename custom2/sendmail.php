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


$mail = new PHPMailer();
$mail->CharSet = "UTF-8";
$mail->IsSMTP();

$mail->Host = "mail.sch.gr";  // specify main and backup server
$mail->SMTPAuth = true;     // turn on SMTP authentication
$mail->Username = "admsup";  // SMTP username
$mail->Password = "Xc4MbY5U"; // SMTP password

$mail->From = "admin@seminars.etwinning.gr";
$mail->FromName = 'Διαχειριστής σεμιναρίων etwinning';

// below we want to set the email address we will be sending our email to.
$mail->AddAddress($USER->email, $USER->lastname . ' ' . $USER->firstname);

$mail->WordWrap = 50;
$mail->IsHTML(true);

$mail->Subject = "Επιμόρφωση etwinning - Sex drugs and rock n roll!";

$message = "Αυτό το μήνυμα στάλθηκε απο την πλατφόρμα επιμόρφωσης seminars.etwinning.gr στα πλαίσια της δραστηριότητας με τίτλο \"...με φίλτρο ή χωρίς;\" της ενότητας: \"Phishing - Ανεπιθύμητη αλληλογραφία\"" ;
$mail->Body    = $message;
$mail->AltBody = $message;

$mail->Send();

?>
 <h1 style="text-align: center;">Το email στάλθηκε στην διευθυνση που έχετε δηλώσει στο προφίλ σας. Ελέγξτε τα email σας να δειτε σε ποιό φάκελο πήγε</h1>
 <p>&nbsp;</p>
 <p style="text-align: center;"><strong>Μπορείτε να κλείσετε τώρα αυτό το παράθυρο</strong><br><i>*Αγνοήστε το μήνυμα λάθους που εμφανίζεται στη σελίδα.</i></p>
 </body>
