<?php
//============================================================+
// File name   : example_001.php
// Begin       : 2008-03-04
// Last Update : 2013-05-14
//
// Description : Example 001 for TCPDF class
//               Default Header and Footer
//
// Author: Nicola Asuni
//
// (c) Copyright:
//               Nicola Asuni
//               Tecnick.com LTD
//               www.tecnick.com
//               info@tecnick.com
//============================================================+

/**
 * Creates an example PDF TEST document using TCPDF
 * @package com.tecnick.tcpdf
 * @abstract TCPDF - Example: Default Header and Footer
 * @author Nicola Asuni
 * @since 2008-03-04
 */

// Include the main TCPDF library (search for installation path).
require_once(dirname(__FILE__).'/tcpdf/tcpdf.php');
require_once(dirname(__FILE__).'/phpQuery-onefile.php');

// disable moodle specific debug messages and any errors in output
define('NO_DEBUG_DISPLAY', true);

require_once('../config.php');
require_once('../lib/filelib.php');

// create new PDF document
$pdf = new TCPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);

// set document information
$pdf->SetCreator("test");
$pdf->SetAuthor('ΚΕ.ΠΛΗ.ΝΕ.Τ. Κυκλάδων');
$pdf->SetTitle('Title');
$pdf->SetSubject('Επιμόρφωση εκπαιδευτικών etwinning - ΚΕΠΛΗΝΕΤ Κυκλάδων');
$pdf->SetKeywords('Επιμόρφωση, ΚΕΠΛΗΝΕΤ, etwinning, Κυκλάδες, guide');

// set default header data
$pdf->SetHeaderData(PDF_HEADER_LOGO, PDF_HEADER_LOGO_WIDTH, 'Title', PDF_HEADER_STRING);

// set header and footer fonts
$pdf->setHeaderFont(Array('courier', '', 8));
$pdf->setFooterFont(Array(PDF_FONT_NAME_DATA, '', PDF_FONT_SIZE_DATA));

// set default monospaced font
$pdf->SetDefaultMonospacedFont(PDF_FONT_MONOSPACED);

// set margins
$pdf->SetMargins(PDF_MARGIN_LEFT, PDF_MARGIN_TOP, PDF_MARGIN_RIGHT);
$pdf->SetHeaderMargin(PDF_MARGIN_HEADER);
$pdf->SetFooterMargin(PDF_MARGIN_FOOTER);

// set auto page breaks
$pdf->SetAutoPageBreak(TRUE, PDF_MARGIN_BOTTOM);

// set image scale factor
$pdf->setImageScale(PDF_IMAGE_SCALE_RATIO);

// set some language-dependent strings (optional)
if (@file_exists(dirname(__FILE__).'/lang/eng.php')) {
    require_once(dirname(__FILE__).'/lang/eng.php');
    $pdf->setLanguageArray($l);
}

// ---------------------------------------------------------

// set font
$pdf->SetFont('dejavusans', '', 10);

// add a page
$pdf->AddPage();

// writeHTML($html, $ln=true, $fill=false, $reseth=false, $cell=false, $align='')
// writeHTMLCell($w, $h, $x, $y, $html='', $border=0, $ln=0, $fill=0, $reseth=true, $align='', $autopadding=true)

// =============================================================
// Get Page from referer
// We need the session context on Moodle
// =============================================================
$opts = array( 'http'=>array( 'method'=>"GET",
              'header'=>"Accept-language: en\r\n" .
               "Cookie: ".session_name()."=".session_id()."\r\n" ) );
$context = stream_context_create($opts);
session_write_close();   // this is the key
//$html = file_get_contents( 'http://seminars.etwinning.gr/mod/page/view.php?id=6273', false, $context);

$html = file_get_contents( $_SERVER["HTTP_REFERER"], false, $context);

// Get CSS
$style = file_get_contents("theme.css");
// =============================================================

phpQuery::newDocumentHTML ( $html );

$simpletitle = pq ( '#region-main h2:first' )->html();
$title = '<h2>' .$simpletitle . '</h2>';
$div = pq ( '#intro' )->html();
$remove = pq ('#fullscreenpadding',pq ( '#intro' ))->html();
$div = str_replace($remove, '', $div);
$div = str_replace('<p></p>', '', $div);
$div = str_replace('<p><br></p>', '<br>', $div);


if ($div == "")
	$div = pq ( '#region-main' )->html();

// Replace all img tags because png images wont load on tcpdf. We convert them to jpg on the fly
$div = str_replace('<img src="http://', '<img src="http://seminars.etwinning.gr/custom/getimage.php?image=http://', $div);


// output the HTML content
 $pdf->writeHTML($style. $title. $div, true, false, true, false, '');


// Close and output PDF document
// This method has several options, check the source code documentation for more information.
 $pdf->Output($simpletitle. '.pdf', 'I');

//============================================================+
// END OF FILE
//============================================================+
?>
