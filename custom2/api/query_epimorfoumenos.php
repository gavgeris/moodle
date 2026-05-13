<?php
require_once('../../config.php');

global $DB;

require_login();

$context = context_system::instance();

if (!is_siteadmin() && !has_capability('moodle/site:viewreports', $context)) {
    die(0);
}

// Get the query parameters from the URL
$surname = $_GET['surname'];
$name = $_GET['name'];
$ep_year = isset($_GET['ep_year']) ? $_GET['ep_year'] : '';

// Build the base SQL query
$sqlstmt = "
select a.lastname, a.firstname, a.email,
       ep_year as xronia,
       a.fullname as course,
       '' as groupname,
       mcp.end_date, mcp.sign_date,  mcp.duration, mcp.start_date,
       a.finalgrade, (select gradepass from mdl_grade_items where courseid = a.courseid and itemtype = 'course') as gradepass
from apotelesmata_vw a, mdl_course_protokolo mcp
where ep_year in ( '2026-27', '2025-26', '2024-25', '2023-24', '2022-23', '2021-22', '2020-21', '2019-20', '2018-19', '2017-18', 'etwinning')
  and lastname = ?
  and firstname like concat('%',?,'%')
  and a.courseid = mcp.courseid
  and a.fullname not like '%επιμορφωτών%'";

$params = array($surname, $name);

// Add year filter if specified
if (!empty($ep_year)) {
    $sqlstmt .= " and ep_year = ?";
    $params[] = $ep_year;
}

$sqlstmt .= " order by ep_year, fullname";

$result = $DB->get_recordset_sql($sqlstmt, $params);
$rows = array();

foreach ($result as $record) {
    $rows[] = $record;
}

echo json_encode($rows);