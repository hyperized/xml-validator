# XmlValidator [![Build Status](https://travis-ci.org/hyperized/xml-validator.svg?branch=master)](https://travis-ci.org/hyperized/xml-validator) [![Scrutinizer Code Quality](https://scrutinizer-ci.com/g/hyperized/xml-validator/badges/quality-score.png?b=master)](https://scrutinizer-ci.com/g/hyperized/xml-validator/?branch=master) [![Code Coverage](https://scrutinizer-ci.com/g/hyperized/xml-validator/badges/coverage.png?b=master)](https://scrutinizer-ci.com/g/hyperized/xml-validator/?branch=master)
A simple PHP XML validator.

Example usage:

```php
<?php

namespace Hyperized\Xml;

require(__DIR__ . '/vendor/autoload.php');

$xmlString = file_get_contents(__DIR__ . '/tests/files/correct.xml');
$xmlFile = __DIR__ . '/tests/files/correct.xml';
$xsdFile = __DIR__ . '/tests/files/simple.xsd';

$xmlValidator = new Validator();

var_dump($xmlValidator->isXMLStringValid($xmlString, $xsdFile));

// Try / Catch incorrect XML
$dirtyXMLString = file_get_contents(__DIR__ . '/tests/files/incorrect.xml');

try {
    $xmlValidator->isXMLStringValid($dirtyXMLString, $xsdFile);
} catch (\Exception $exception) {
    var_dump($xmlValidator->getPrettyErrors());
}

// Directly from file
var_dump($xmlValidator->isXMLFileValid($xmlFile, $xsdFile));
```