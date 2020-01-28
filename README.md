# [hyperized/xml-validator](https://packagist.org/packages/hyperized/xml-validator)

![Run tests](https://github.com/hyperized/xml-validator/workflows/Run%20tests/badge.svg) [![Scrutinizer Code Quality](https://scrutinizer-ci.com/g/hyperized/xml-validator/badges/quality-score.png?b=master)](https://scrutinizer-ci.com/g/hyperized/xml-validator/?branch=master) [![Code Coverage](https://scrutinizer-ci.com/g/hyperized/xml-validator/badges/coverage.png?b=master)](https://scrutinizer-ci.com/g/hyperized/xml-validator/?branch=master) [![FOSSA Status](https://app.fossa.io/api/projects/git%2Bgithub.com%2Fhyperized%2Fxml-validator.svg?type=shield)](https://app.fossa.io/projects/git%2Bgithub.com%2Fhyperized%2Fxml-validator?ref=badge_shield) [![License](https://poser.pugx.org/hyperized/xml-validator/license)](https://packagist.org/packages/hyperized/xml-validator)

A simple PHP XML validator.

## Installation

```shell script
composer require hyperized/xml-validator
```

## Usage

```php
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
```

## License
[![FOSSA Status](https://app.fossa.io/api/projects/git%2Bgithub.com%2Fhyperized%2Fxml-validator.svg?type=large)](https://app.fossa.io/projects/git%2Bgithub.com%2Fhyperized%2Fxml-validator?ref=badge_large)
