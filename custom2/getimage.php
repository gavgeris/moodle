<?php
// disable moodle specific debug messages and any errors in output
define('NO_DEBUG_DISPLAY', true);

require_once('../config.php');
require_once('../lib/filelib.php');
require_once('imageUtils.php');

$opts = array( 'http'=>array( 'method'=>"GET",
              'header'=>"Accept-language: en\r\n" .
               "Cookie: ".session_name()."=".session_id()."\r\n" ) );

$context = stream_context_create($opts);
session_write_close();   // this is the key
$image =  file_get_contents( $_REQUEST["image"], false, $context);
$pngimage = imagecreatefromstring ($image);
header('Content-Type: image/jpg');
//echo $image;

	imagejpeg($pngimage, null, 75);
?>