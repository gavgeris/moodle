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
select a.lastname, a.firstname, a.email,
       ep_year as xronia,
       a.fullname as course,
       '' as groupname,
       mcp.end_date, mcp.sign_date,  mcp.duration, mcp.start_date,
       a.finalgrade, (select gradepass from mdl_grade_items where courseid = a.courseid and itemtype = 'course') as gradepass
from apotelesmata_vw a, mdl_course_protokolo mcp
where ep_year in ( '2023-24', '2022-23', '2021-22', '2020-21', '2019-20', '2018-19', 'etwinning')
  and lastname = ?
  and firstname like concat('%',?,'%')
  and a.courseid = mcp.courseid
  and a.fullname not like '%επιμορφωτών%'
order by ep_year, fullname
";

$result = $DB->get_recordset_sql($sqlstmt, array($surname, $name));
$rows = array();

foreach ($result as $record) {
   $rows[] = $record;
}

echo json_encode($rows);



