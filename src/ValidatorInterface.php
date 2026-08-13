<?php

declare(strict_types=1);

namespace Hyperized\Xml;

use Hyperized\Xml\Exceptions\EmptyFile;
use Hyperized\Xml\Exceptions\FileCouldNotBeOpenedException;
use Hyperized\Xml\Exceptions\FileDoesNotExist;
use Hyperized\Xml\Exceptions\InvalidXml;
use Hyperized\Xml\Exceptions\XmlValidatorException;

/**
 * Each check comes in two forms: a predicate that answers yes or no, and a
 * validate* method that throws with the detail. The predicates are built on the
 * throwing methods, so the two can never disagree.
 *
 * Implementations hold no error state, which makes them safe to share.
 */
interface ValidatorInterface
{
    /**
     * Never throws a XmlValidatorException. Use validateXMLFile() for the reason.
     */
    public function isXMLFileValid(string $xmlPath, ?string $xsdPath = null): bool;

    /**
     * Never throws a XmlValidatorException. Use validateXMLString() for the reason.
     */
    public function isXMLStringValid(string $xml, ?string $xsdPath = null): bool;

    /**
     * @throws FileDoesNotExist              Neither the XML nor the XSD is there.
     * @throws FileCouldNotBeOpenedException The XML exists but could not be read.
     * @throws EmptyFile                     The XML file holds nothing.
     * @throws InvalidXml                    Malformed, or rejected by the schema.
     */
    public function validateXMLFile(string $xmlPath, ?string $xsdPath = null): void;

    /**
     * @throws FileDoesNotExist No XSD at the given path.
     * @throws InvalidXml       Malformed, empty once trimmed, or rejected by the schema.
     */
    public function validateXMLString(string $xml, ?string $xsdPath = null): void;

    public function getVersion(): string;

    public function setVersion(string $version): void;

    public function getEncoding(): string;

    public function setEncoding(string $encoding): void;
}
