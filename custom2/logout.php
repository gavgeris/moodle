<?php


// disable moodle specific debug messages and any errors in output
require_once('../config.php');
require_once('../lib/filelib.php');

session_unset();
session_destroy();


$cas_url = "https://sso.sch.gr/logout";
$cas_url = $cas_url . "?" . "service="
        . urlencode("https://seminars.etwinning.gr/login");


header('Location: '.$cas_url);
