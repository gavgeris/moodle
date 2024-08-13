<?php

// disable moodle specific debug messages and any errors in output
define('NO_DEBUG_DISPLAY', true);

require_once('../config.php');
require_once('../lib/filelib.php');

global $DB;
$url = $_SERVER["HTTP_REFERER"];
parse_str(parse_url($url, PHP_URL_QUERY), $params);

$id = $params["id"];


require_login();
if (isguestuser()) {
    print_error('noguest');
}

$sqlstmt = "select *
from 	mdl_forum_discussions mfd,
	(select concat('ΕΒΔΟΜΑΔΑ ', mcs.section, ': ', mc.name) as forum_name, mcm.course As forum_course
	   from mdl_course_modules mcm, mdl_assign mc, mdl_course_sections mcs
	  where mcm.id = ?
	    and mcm.instance = mc.id
	    and mcm.section = mcs.id
	 ) f
where mfd.course = f.forum_course
  and mfd.name = f.forum_name";


$value = $DB->get_field_sql($sqlstmt, array($id));
print $value;
if ($value == "") {
	header('Location: '.$url);
} else {
	$newURL = "http://seminars.etwinning.gr/mod/forum/discuss.php?d=".$value;
	header('Location: '.$newURL);
}


?>

