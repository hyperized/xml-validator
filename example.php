<?php

namespace Hyperized\Xml;

require __DIR__ . '/vendor/autoload.php';

$xmlString = file_get_contents(__DIR__ . '/tests/files/correct.xml');
$dirtyXMLString = file_get_contents(__DIR__ . '/tests/files/incorrect.xml');
$xmlFile = __DIR__ . '/tests/files/correct.xml';
$xsdFile = __DIR__ . '/tests/files/simple.xsd';

$validator = new Validator();

// String validation
print_r($validator->isXMLStringValid($xmlString)); // 1
print_r($validator->isXMLStringValid($xmlString, $xsdFile)); // 1

// File validation
print_r($validator->isXMLFileValid($xmlFile)); // 1
print_r($validator->isXMLFileValid($xmlFile, $xsdFile)); // 1

// Error handling
try {
    $validator->isXMLStringValid($dirtyXMLString, $xsdFile);
} catch (Exceptions\InvalidXmlException $exception)
{
    print_r($exception->getMessage()); //  xmlParseEntityRef: no name\n The document has no document element.
}
