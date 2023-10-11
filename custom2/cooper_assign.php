<?php

require_once('../config.php');
require_once($CFG->dirroot . '/mod/assign/locallib.php');
require_once(dirname(__FILE__).'/phpQuery-onefile.php');
//header('Content-Type: application/json');
$url = $_SERVER["HTTP_REFERER"];
parse_str(parse_url($url, PHP_URL_QUERY), $params);

$id = $params["id"];
//$sharedkey = $params["sharedkey"];
//$action = $params["action"];

//$id = required_param('id', PARAM_INT);
$sharedkey = optional_param('sharedkey', null, PARAM_TEXT);
$action = optional_param('action', '', PARAM_ACTION);
$members = optional_param('members', 1, PARAM_INT);
$mode = optional_param('mode', 'simple', PARAM_TEXT);
$groupmode = optional_param('groupmode', 0, PARAM_INT); // 0: No Group Mode, 1: Group Mode

if (strcmp($mode,"simple") == 0) {
	$members++;
}

list ($course, $cm) = get_course_and_cm_from_cmid($id, 'assign');
//print_r($cm);
$cm = get_coursemodule_from_id('assign', $id, 0, false, MUST_EXIST);
// Get the current group id.
$currentgroupid = groups_get_activity_group($cm);
//echo ($currentgroupid);
//die(0);
// Get the current group name from the group id.
$currentgroupname = groups_get_group_name($currentgroupid);

if (strcmp($action,"getKey") == 0) {
  $sharedkey = $DB->get_field('assign_cooper_keys', 'shared_key',array('userid'=>$USER->id, 'assign_id'=>$id),$strictness=IGNORE_MISSING);
  if ($sharedkey == false)
	$sharedkey = "";
  $object->sharedkey = $sharedkey;
  echo json_encode($object);
} elseif (strcmp($action,"save") == 0) {
 if ($sharedkey != null) {
    //echo "Action:Save<BR>";
	 $exists = $DB->get_record('assign_cooper_keys', array('userid'=>$USER->id, 'assign_id'=>$id), $fields='id', $strictness=IGNORE_MISSING) ;

	 if (!$exists) {
		$record = new stdClass();
		$record->userid       = $USER->id;
		$record->shared_key   = $sharedkey;
		$record->assign_id 	  = $id;
		$record->groupid 	  = $currentgroupid;

		$lastinsertid = $DB->insert_record_raw('assign_cooper_keys', $record, false);
	 } else {
		 $exists->shared_key = $sharedkey;
		 $DB->update_record('assign_cooper_keys', $exists, false);
	 }
	 $object->status = "OK";
	 echo json_encode($object);
 }
} elseif (strcmp($action,"find") == 0) {
    //echo "Action:Find<BR>";
	/* Get current associations. Maybe equal to members or less */
	$sqlstmt1 = "SELECT user2, mack.shared_key
		FROM {assign_cooper} mac, {assign_cooper_keys} mack
		WHERE user1 = ?
		  AND mac.assign_id = ?
		  AND mac.user2 = mack.userid
		  AND mac.assign_id = mack.assign_id";
		if ($groupmode == 1) {
			$sqlstmt1 .= "AND mac.groupid = ?";
			$sqlstmt1 .= "AND mac.groupid = mack.groupid";
		}
	$result = $DB->get_records_sql($sqlstmt1, array($USER->id, $id, $currentgroupid));
//    echo ("======== Partners ========<BR>");
//	foreach ($result as $rec) { 
//	  echo ($rec->user2 ." - ". $rec->shared_key . "</BR>");
//	}
//    echo ("========================<BR>");
  
	//echo json_encode($result);

	/* Remaining associations if current associations are less than $members */
	$noofrecords = max(0, $members - sizeof($result));
	
	/* Find new associations. We need same group, activity and we limit the records so to get only those we need to reach $members
	 Sort by counter to get first those partners that they have the lesser associations.
	 The WHERE counter clause ensures that partners that have already $members associations will not be returned. 
	 TO_DO: When the deadline is near, these partners must be returned to ensure that delayed students will find a partner
	*/
	if ($groupmode == 0) {
		$sqlstmt ="
		SELECT *
		FROM (
			SELECT userid, (SELECT COUNT(*) FROM mdl_assign_cooper WHERE user2 = mack.userid AND assign_id = mack.assign_id AND groupid = mack.groupid) AS counter
				FROM {assign_cooper_keys} mack
				WHERE userid != ?
				  AND assign_id = ?	
				  AND groupid = ?
				  AND NOT EXISTS (SELECT 1 FROM mdl_assign_cooper WHERE user1 = ? AND user2 = mack.userid AND assign_id = mack.assign_id AND groupid = mack.groupid)
			) a
		WHERE counter < ?
		ORDER BY counter
		LIMIT " .$noofrecords ;
		$result2 = $DB->get_records_sql($sqlstmt, array($USER->id, $id, $currentgroupid, $USER->id, $members ));
	} else {
		$sqlstmt ="
		SELECT *
		FROM (
			SELECT userid, (SELECT COUNT(*) FROM mdl_assign_cooper WHERE user2 = mack.userid AND assign_id = mack.assign_id) AS counter
				FROM {assign_cooper_keys} mack
				WHERE userid != ?
				  AND assign_id = ?	
				  AND NOT EXISTS (SELECT 1 FROM mdl_assign_cooper WHERE user1 = ? AND user2 = mack.userid AND assign_id = mack.assign_id)
			) a
		WHERE counter < ?
		ORDER BY counter
		LIMIT " .$noofrecords ;
		$result2 = $DB->get_records_sql($sqlstmt, array($USER->id, $id, $USER->id, $members));
	}
	// Write the associations to assign_cooper table. Everytime the same associations will be displayed
	foreach ($result2 as $rec) { 
		$record = new stdClass();
		$record->user1       = $USER->id;
		$record->assign_id 	 = $id;
		$record->groupid 	 = $currentgroupid;
		$record->user2       = $rec->userid;
		$DB->insert_record_raw('assign_cooper', $record, false);
		
		// On sync mode we bind the 2 users for each other. So we write a record for the 2nd user too.
		if (strcmp($mode,"sync") == 0) {
			$record = new stdClass();
			$record->user2       = $USER->id;
			$record->assign_id 	 = $id;
			$record->groupid 	 = $currentgroupid;
			$record->user1       = $rec->userid;
			$DB->insert_record_raw('assign_cooper', $record, false);
		}
	}
	$result = $DB->get_records_sql($sqlstmt1, array($USER->id, $id, $currentgroupid));
	echo json_encode(array_values($result));
}
