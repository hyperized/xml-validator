<?php

namespace Hyperized\Xml;

require __DIR__ . '/vendor/autoload.php';

$xmlString = file_get_contents(__DIR__ . '/tests/files/correct.xml');
$dirtyXMLString = file_get_contents(__DIR__ . '/tests/files/incorrect.xml');
$xmlFile = __DIR__ . '/tests/files/correct.xml';
$xsdFile = __DIR__ . '/tests/files/simple.xsd';

$validator = new Validator();

// String validation
print_r($validator->isXMLStringValid($xmlString)); // true
print_r($validator->isXMLStringValid($xmlString, $xsdFile)); // true

// File validation
print_r($validator->isXMLFileValid($xmlFile)); // true
print_r($validator->isXMLFileValid($xmlFile, $xsdFile)); // true

// Error handling
try {
    $validator->isXMLStringValid($dirtyXMLString, $xsdFile);
} catch (Exceptions\InvalidXmlException $exception)
{
    print_r($exception->getMessage()); //  xmlParseEntityRef: no name \n The document has no document element.
}
