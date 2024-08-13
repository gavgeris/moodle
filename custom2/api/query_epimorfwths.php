<?php
require_once('../../config.php');

global $DB;

require_login();

if (!is_siteadmin()) {
    die(0);
}

// Get the query parameter from the URL
$surname = $_GET['surname'];
$name = $_GET['name'];

// SQL query to fetch data based on the provided parameter
$sqlstmt = "
SELECT DISTINCT mu.`lastname`, mu.`firstname`, mu.email,
                mcc.name as xronia,
                (select fullname from mdl_course where id = ge.courseid) as course,
                ge.groupname,
                mcp.end_date, mcp.sign_date,  mcp.duration, mcp.start_date
FROM group_epimorfwth ge, mdl_course_categories mcc, mdl_course mc LEFT JOIN mdl_course_protokolo mcp ON (mcp.courseid = mc.id), mdl_user mu
WHERE ge.`courseid` = mc.`id`
  AND mc.`category` = mcc.`id`
  AND mcc.name IN ( '2023-24', '2022-23', '2021-22', '2020-21', '2019-20', '2018-19','etwinning')
  AND groupname NOT LIKE '%Συντονιστές Επιμορφωτών%'
  AND ge.epimorfwths = mu.id
  and lastname like ?
  and firstname like concat('%',?,'%')
 
";

$result = $DB->get_recordset_sql($sqlstmt, array($surname, $name));
$rows = array();

foreach ($result as $record) {
   $rows[] = $record;
}

echo json_encode($rows);



