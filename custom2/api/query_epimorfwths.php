<?php
require_once('../../config.php');

global $DB;

require_login();

if (!is_siteadmin()) {
    die(0);
}

// Get the query parameters from the URL
$surname = $_GET['surname'];
$name = $_GET['name'];
$ep_year = isset($_GET['ep_year']) ? $_GET['ep_year'] : '';

// Build the base SQL query
$sqlstmt = "
SELECT DISTINCT mu.`lastname`, mu.`firstname`, mu.email,
                mcc.name as xronia,
                (select fullname from mdl_course where id = ge.courseid) as course,
                ge.groupname,
                mcp.end_date, mcp.sign_date,  mcp.duration, mcp.start_date
FROM group_epimorfwth ge, mdl_course_categories mcc, mdl_course mc LEFT JOIN mdl_course_protokolo mcp ON (mcp.courseid = mc.id), mdl_user mu
WHERE ge.`courseid` = mc.`id`
  AND mc.`category` = mcc.`id`
  AND mcc.name IN ( '2025-26', '2024-25', '2023-24', '2022-23', '2021-22', '2020-21', '2019-20', '2018-19', '2017-18', 'etwinning')
  AND groupname NOT LIKE '%Συντονιστές Επιμορφωτών%'
  AND ge.epimorfwths = mu.id
  and lastname like ?
  and firstname like concat('%',?,'%')";

$params = array($surname, $name);

// Add year filter if specified
if (!empty($ep_year)) {
    $sqlstmt .= " and mcc.name = ?";
    $params[] = $ep_year;
}

$result = $DB->get_recordset_sql($sqlstmt, $params);
$rows = array();

foreach ($result as $record) {
    $rows[] = $record;
}

echo json_encode($rows);