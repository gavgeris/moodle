<?php

define('NO_DEBUG_DISPLAY', true);

require_once('../config.php');
require_once('../lib/filelib.php');
require 'DataGrid.php';
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require './PHPMailer/PHPMailer.php';
require './PHPMailer/SMTP.php';

global $DB;

require_login();
if (isguestuser()) {
    print_error('noguest');
}

//$sqlstmt1 = file_get_contents('sql/sql_monitor_epimorfwtes.sql'); 

$ABSENT_DAYS_LIMIT = 5; 
$UNGRADED_LIMIT_PER_WEEK = 5;
$UNGRADED_LIMIT_SUM = 20;
$EPIMORFWTHS_PLUS_LIMIT = 30;

$REASON_DAYS = 0;
$REASON_LIMIT_WEEK = 1;
$REASON_LIMIT_SUM = 2;
$REASON_LIMIT_EPIMORFWTH_PLUS = 3;

if (isset($_REQUEST["action"]) && $_REQUEST["action"] == "warn") {
	$options["name"] = $_REQUEST["epimorfwths_name"];
	$options["email"] = $_REQUEST["email"];
	$options["days"] = $_REQUEST["days"];
	$options["warn_type"] = $_REQUEST["warn_type"];
	$options["plithos"] = $_REQUEST["plithos"];
	$options["courseid"] = $_REQUEST["courseid"];
	$options["course"] = $_REQUEST["course"];
	$options["subject"] = "[Σεμινάρια etwinning] Εκκρεμείς εργασίες";
	sendmail($options);
	warn_epimorfwth($options);
	//header('Location: ' . $_SERVER['HTTP_REFERER']);
} else if (isset($_REQUEST["action"]) && $_REQUEST["action"] == "epimorfwths_plus") {
	var_dump($_REQUEST["action"]);
	$options["courseid"] = $_REQUEST["courseid"];
    $options["email"] = $_REQUEST["email"];
	$options["course"] = $_REQUEST["course"];
	$options["groupname"] = $_REQUEST["groupname"];
    $options["warn_type"] = $_REQUEST["warn_type"];
	$result = set_epimorfwth_plus($options);
	if (!$result) return;
	$options["warn_type"] = "plus";
	$options["subject"] = "[etwinning seminars] Διόρθωση εργασιών " . $options["groupname"];
	sendmail($options);
} else if (isset($_REQUEST["action"]) && $_REQUEST["action"] == "resetwarn") {
    $options["courseid"] = $_REQUEST["courseid"];
    $options["email"] = $_REQUEST["email"];
    $options["course"] = $_REQUEST["course"];
    $options["groupname"] = $_REQUEST["groupname"];
    $options["warn_type"] = $_REQUEST["warn_type"];
    $result = reset_warn($options);
} else if (isset($_REQUEST["action"]) && $_REQUEST["action"] == "resetepimorfwth_plus") {
    $options["courseid"] = $_REQUEST["courseid"];
    $options["email"] = $_REQUEST["email"];
    $options["course"] = $_REQUEST["course"];
    $options["groupname"] = $_REQUEST["groupname"];
    $options["warn_type"] = $_REQUEST["warn_type"];
    $result = reset_epimorfwth_plus($options);
	$options["warn_type"] = "plus_reset";
	$options["subject"] = "[etwinning seminars] Αποδέσμευση απο διόρθωση εργασιών " . $options["groupname"];
	sendmail($options);
}
/*
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
*/
function sendmail($options) {
	global $DB;
	
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

	$mail->Subject = $options["subject"];

	//echo $record->emailpsd;
	//$mail->AddAddress($options["email"], $options["name"]);
	//$mail->AddAddress("gavgeri@sch.gr", "george Avgeris");
	$mail->AddAddress($options["email"], $options["name"]);
	$mail->addBCC("admin@seminars.etwinning.gr", 'Διαχειριστής σεμιναρίων etwinning');
	$message = file_get_contents("./files/email_epimorfwtes" . $options["warn_type"] . ".html");
	
	$message = str_replace('%COURSE%',$options["course"],$message);
	$message = str_replace('%GROUPNAME%',$options["groupname"],$message);	
	$message = str_replace('%DAYS%',$options["days"],$message);
	$message = str_replace('%PLITHOS%',$options["plithos"],$message);

	// Get 3 days later
	$date = getdate();
	$date=date_create($date["year"]. "-" . $date["mon"] . "-" . $date["mday"]);
	date_add($date,date_interval_create_from_date_string("3 days"));

	$message = str_replace('%DEADLINE%',date_format($date,"d/m/Y"),$message);
	$mail->Body    = $message;
	$mail->AltBody = $message;
	$mail->Send();
	$mail = null;
	var_dump( $options["email"]);	
}

function warn_epimorfwth($options) {
	global $DB;
	
	$row["courseid"] = $options["courseid"];
	$row["epimorfwths"] = $options["email"];
	$row["warn_type"] = $options["warn_type"];
	$row["warn_time"] = time();
	$DB->insert_record("epimorfwtes_warnings", $row, true);
}

function reset_warn($options) {
    global $DB;

    $sql = 'DELETE FROM mdl_epimorfwtes_warnings WHERE epimorfwths =  ? AND courseid = ? AND warn_type IN (1,2)';
    $DB->execute($sql,[$options["email"], $options["courseid"]]);
}

function reset_epimorfwth_plus(&$options) {
    global $DB;
    $sql = 'SELECT mra.userid ' .
        'FROM mdl_role_assignments mra, mdl_context mc '.
        'WHERE mra.contextid = mc.id '.
        'AND mra.roleid=11 ' .
        'AND mc.instanceid = ?';
    $epimorfwths_plus_id = "";
    $epimorfwths_plus_id = $DB->get_field_sql($sql,[$options["courseid"]]);
    if (epimorfwths_plus_id == "") return false;

    $sql = 'SELECT email FROM mdl_user WHERE id = ?';
    $email_epimorfwth = $options["email"]; // Keep email κανονικου επιμορφωτη
    $email = $DB->get_field_sql($sql,[$epimorfwths_plus_id]);
    $options["email"] = $email;

    $sql = 'SELECT id FROM mdl_groups WHERE courseid = ? AND name = ?';
    $group_id = $DB->get_field_sql($sql,[$options["courseid"],$options["groupname"]]);

    $sql = 'DELETE FROM mdl_groups_members WHERE groupid IN (SELECT id FROM mdl_groups WHERE courseid = ?) AND userid = ? LIMIT 1';
    $DB->execute($sql,[$options["courseid"], $epimorfwths_plus_id]);

    $sql = 'DELETE FROM mdl_epimorfwtes_warnings WHERE courseid = ? AND epimorfwths = ? and warn_type = ?';
    $DB->execute($sql,[$options["courseid"], $email_epimorfwth , $options["warn_type"] ]);

    //var_dump($group_id);
    return true;
}



function set_epimorfwth_plus(&$options) {
	global $DB;
	$sql = 'SELECT mra.userid ' .
			'FROM mdl_role_assignments mra, mdl_context mc '.
		   'WHERE mra.contextid = mc.id '.
			'AND mra.roleid=11 ' .
			'AND mc.instanceid = ?';
	$epimorfwths_plus_id = "";
	$epimorfwths_plus_id = $DB->get_field_sql($sql,[$options["courseid"]]);
	if (epimorfwths_plus_id == "") return false;
	
	$sql = 'SELECT email FROM mdl_user WHERE id = ?';
    $email_epimorfwth = $options["email"]; // Keep email κανονικου επιμορφωτη
	$email = $DB->get_field_sql($sql,[$epimorfwths_plus_id]);
    $options["email"] = $email;

	$sql = 'SELECT id FROM mdl_groups WHERE courseid = ? AND name = ?';
	$group_id = $DB->get_field_sql($sql,[$options["courseid"],$options["groupname"]]);

	$sql = 'DELETE FROM mdl_groups_members WHERE groupid IN (SELECT id FROM mdl_groups WHERE courseid = ?) AND userid = ? LIMIT 1';
	$DB->execute($sql,[$options["courseid"], $epimorfwths_plus_id]);
	
	echo $DB->insert_record("groups_members",["groupid" =>$group_id, "userid" => $epimorfwths_plus_id]);

    $row["courseid"] = $options["courseid"];
    $row["epimorfwths"] = $email_epimorfwth;
    $row["warn_type"] = $options["warn_type"];
    $row["warn_time"] = time();
    $DB->insert_record("epimorfwtes_warnings", $row, true);


	//var_dump($group_id);
	return true;
}

?>