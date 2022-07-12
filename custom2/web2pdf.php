<?php

/*
Create PDF using the web2pdfconvert.com site
Gets the id from url and constructs the new URL depending if sourceURL is a page or assignment.

simpleassign.php displays the assign without headers
simplepage.php displays the page without headers

*/

include 'phpQuery-onefile.php';
require_once('../config.php');
require_once('../lib/filelib.php');

$url = $_SERVER["HTTP_REFERER"];
parse_str(parse_url($url, PHP_URL_QUERY), $params);

$id = $params["id"];

 $cm = get_coursemodule_from_id('page', $id);
 
if ($cm == null) {
	$newurl = 'http://www.web2pdfconvert.com/engine.aspx?curl=' ."http://seminars.etwinning.gr/custom/simpleassign.php?id=".$id . '&ref=form';
	$newurl = "http://seminars.etwinning.gr/custom/simpleassign.php?id=".$id;
} else {
	$newurl = 'http://www.web2pdfconvert.com/engine.aspx?curl=' ."http://seminars.etwinning.gr/custom/simplepage.php?id=".$id . '&ref=form';
	$newurl = "http://seminars.etwinning.gr/custom/simplepage.php?id=".$id ;
}
//echo $newurl;
$post = [
    'conversion_source' => 'uri',
    'src' => urlencode($newurl)
];

$ch = curl_init();
curl_setopt($ch, CURLOPT_URL,"https://pdfcrowd.com/form/json/convert/uri/v2/");
curl_setopt($ch, CURLOPT_POST, 2);
//curl_setopt($ch, CURLOPT_POSTFIELDS,$vars);  //Post Fields
curl_setopt($ch, CURLOPT_POSTFIELDS,"conversion_source=uri&src=".urlencode($newurl));
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);

$headers = [
    'Accept: text/javascript, text/html,application/xhtml+xml,application/xml',
    'Accept-Encoding:  deflate, br',
    'Accept-Language: en-US,en;q=0.5',
    'Cache-Control: no-cache',
    'Content-Type: application/x-www-form-urlencoded; charset=utf-8',
    'Host: pdfcrowd.com',
	'Referer: https://pdfcrowd.com/',
    'User-Agent: Mozilla/5.0 (X11; Ubuntu; Linux i686; rv:28.0) Gecko/20100101 Firefox/28.0',
    'X-MicrosoftAjax: Delta=true'
];

curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

$htmldoc = curl_exec ($ch);
curl_close ($ch);
//$htmldoc = str_replace($siteprefix, "http://www.discoverandros.gr/ws/accuweather.php?paramurl=", $htmldoc);
//$htmldoc = str_replace("href=\"/el/gr/ermoupoli/2282688/", "href=\"http://www.discoverandros.gr/ws/accuweather.php?paramurl=", $htmldoc);
//$htmldoc = str_replace("//pagead2.googlesyndication.com/pagead/js/adsbygoogle.js", "", $htmldoc);
//$htmldoc = str_replace("//www.googleadservices.com/pagead/conversion.js", "", $htmldoc);
//$htmldoc = str_replace("//googleads.g.doubleclick.net/pagead/viewthroughconversion", "", $htmldoc);

//phpQuery::newDocumentHTML($htmldoc);
//echo $htmldoc;
$response = json_decode($htmldoc);

$pdffile = "https://pdfcrowd.com" .$response->uri;
//echo phpQuery::getDocument();
header('Location: '. $pdffile);
