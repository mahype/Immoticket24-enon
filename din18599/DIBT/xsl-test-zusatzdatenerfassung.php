<?php


$xsl_filename = __DIR__ . '/kontrolldatei-dibt-2024.xsl';
$xml_filename = __DIR__ . '/bedarfsausweis.xml';

$xsldoc = new DOMDocument();
$xsldoc->load($xsl_filename);

$xmldoc = new DOMDocument();
$xmldoc->load($xml_filename);

$dom = new DomDocument;
$dom->loadXML(file_get_contents( $xml_filename ) );
if ($dom->schemaValidate($xsl_filename)) {
    // Valid response from service
    echo 'Valid response';
} else {
    // Invalid response
    echo 'Invalid response';
}