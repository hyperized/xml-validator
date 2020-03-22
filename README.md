# [hyperized/xml-validator](https://packagist.org/packages/hyperized/xml-validator)

[![Buy us a tree](https://img.shields.io/badge/Treeware-%F0%9F%8C%B3-lightgreen)](https://plant.treeware.earth/hyperized/xml-validator) ![Run tests](https://github.com/hyperized/xml-validator/workflows/Run%20tests/badge.svg) [![Scrutinizer Code Quality](https://scrutinizer-ci.com/g/hyperized/xml-validator/badges/quality-score.png?b=master)](https://scrutinizer-ci.com/g/hyperized/xml-validator/?branch=master) [![Code Coverage](https://scrutinizer-ci.com/g/hyperized/xml-validator/badges/coverage.png?b=master)](https://scrutinizer-ci.com/g/hyperized/xml-validator/?branch=master) [![FOSSA Status](https://app.fossa.io/api/projects/git%2Bgithub.com%2Fhyperized%2Fxml-validator.svg?type=shield)](https://app.fossa.io/projects/git%2Bgithub.com%2Fhyperized%2Fxml-validator?ref=badge_shield) [![License](https://poser.pugx.org/hyperized/xml-validator/license)](https://packagist.org/packages/hyperized/xml-validator)

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

## Treeware

You're free to use this package, but if it makes it to your production environment you are required to buy the world a tree.

It’s now common knowledge that one of the best tools to tackle the climate crisis and keep our temperatures from rising above 1.5C is to <a href="https://www.bbc.co.uk/news/science-environment-48870920">plant trees</a>. If you support this package and contribute to the Treeware forest you’ll be creating employment for local families and restoring wildlife habitats.

You can buy trees here [offset.earth/treeware](https://plant.treeware.earth/hyperized/xml-validator)

Read more about Treeware at [treeware.earth](http://treeware.earth)

## License
[![FOSSA Status](https://app.fossa.io/api/projects/git%2Bgithub.com%2Fhyperized%2Fxml-validator.svg?type=large)](https://app.fossa.io/projects/git%2Bgithub.com%2Fhyperized%2Fxml-validator?ref=badge_large)
