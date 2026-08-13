<?php

declare(strict_types=1);

use Hyperized\Xml\Exceptions\InvalidXml;
use Hyperized\Xml\Exceptions\XmlValidatorException;
use Hyperized\Xml\Validator;

require __DIR__ . '/vendor/autoload.php';

$xsdFile = __DIR__ . '/tests/files/simple.xsd';
$xmlFile = __DIR__ . '/tests/files/correct.xml';
$brokenFile = __DIR__ . '/tests/files/incorrect.xml';
$missingFile = __DIR__ . '/tests/files/does_not_exist.xml';

$validator = new Validator();

// Validate a document, and report why it failed.
foreach ([$xmlFile, $brokenFile, $missingFile] as $path) {
    try {
        $validator->validateXMLFile($path, $xsdFile);

        printf("%s: valid\n", basename($path));
    } catch (InvalidXml $exception) {
        // Malformed, or rejected by the schema. Line and column survive.
        printf("%s: invalid\n", basename($path));

        foreach ($exception->getErrors() as $error) {
            printf("  line %d column %d: %s\n", $error->line, $error->column, trim($error->message));
        }
    } catch (XmlValidatorException $exception) {
        // Missing, unreadable or empty file.
        printf("%s: %s: %s\n", basename($path), $exception::class, $exception->getMessage());
    }
}

// A document you already hold goes through validateXMLString() instead.
$xml = file_get_contents($xmlFile);

if ($xml === false) {
    exit('Could not read ' . $xmlFile . PHP_EOL);
}

try {
    $validator->validateXMLString($xml, $xsdFile);

    echo "in-memory document: valid\n";
} catch (XmlValidatorException $exception) {
    printf("in-memory document: %s\n", $exception->getMessage());
}

// When a failure needs no explanation, the predicates run the same check and
// return false rather than throwing.
var_dump($validator->isXMLFileValid($xmlFile, $xsdFile));  // true
var_dump($validator->isXMLFileValid($brokenFile));         // false
var_dump($validator->isXMLStringValid($xml));              // true
