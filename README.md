# xml-validator

[![Build Status](https://travis-ci.org/hyperized/xml-validator.svg?branch=master)](https://travis-ci.org/hyperized/xml-validator) [![Scrutinizer Code Quality](https://scrutinizer-ci.com/g/hyperized/xml-validator/badges/quality-score.png?b=master)](https://scrutinizer-ci.com/g/hyperized/xml-validator/?branch=master) [![Code Coverage](https://scrutinizer-ci.com/g/hyperized/xml-validator/badges/coverage.png?b=master)](https://scrutinizer-ci.com/g/hyperized/xml-validator/?branch=master) [![FOSSA Status](https://app.fossa.io/api/projects/git%2Bgithub.com%2Fhyperized%2Fxml-validator.svg?type=shield)](https://app.fossa.io/projects/git%2Bgithub.com%2Fhyperized%2Fxml-validator?ref=badge_shield)

A simple PHP XML validator.

Example usage:

```php
<?php

namespace Hyperized\Xml;

require __DIR__ . '/vendor/autoload.php';

$xmlString = file_get_contents(__DIR__ . '/tests/files/correct.xml');
$xmlFile = __DIR__ . '/tests/files/correct.xml';
$xsdFile = __DIR__ . '/tests/files/simple.xsd';

$xmlValidator = new Validator();

var_dump($xmlValidator->isXMLStringValid($xmlString, $xsdFile));

// Try / Catch incorrect XML
$dirtyXMLString = file_get_contents(__DIR__ . '/tests/files/incorrect.xml');

if (!$xmlValidator->isXMLStringValid($dirtyXMLString, $xsdFile)) {
    var_dump($xmlValidator->getErrors());
    var_dump($xmlValidator->getPrettyErrors());
}

// Directly from file
var_dump($xmlValidator->isXMLFileValid($xmlFile, $xsdFile));
```

## License
[![FOSSA Status](https://app.fossa.io/api/projects/git%2Bgithub.com%2Fhyperized%2Fxml-validator.svg?type=large)](https://app.fossa.io/projects/git%2Bgithub.com%2Fhyperized%2Fxml-validator?ref=badge_large)