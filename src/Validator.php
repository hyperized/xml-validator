<?php

declare(strict_types=1);

namespace Hyperized\Xml;

use DOMDocument;
use Hyperized\Xml\Constants\ErrorMessages;
use Hyperized\Xml\Constants\Strings;
use Hyperized\Xml\Exceptions\InvalidXml;
use Hyperized\Xml\Exceptions\XmlValidatorException;
use Hyperized\Xml\Types\Files\Xml;
use Hyperized\Xml\Types\Files\Xsd;
use LibXMLError;

/**
 * Based on: http://stackoverflow.com/a/30058598/1757763
 */
final class Validator implements ValidatorInterface
{
    public function __construct(
        private string $version = Strings::VERSION,
        private string $encoding = Strings::UTF_8
    ) {
    }

    public function isXMLFileValid(string $xmlPath, ?string $xsdPath = null): bool
    {
        try {
            $this->validateXMLFile($xmlPath, $xsdPath);

            return true;
        } catch (XmlValidatorException) {
            return false;
        }
    }

    public function isXMLStringValid(string $xml, ?string $xsdPath = null): bool
    {
        try {
            $this->validateXMLString($xml, $xsdPath);

            return true;
        } catch (XmlValidatorException) {
            return false;
        }
    }

    public function validateXMLFile(string $xmlPath, ?string $xsdPath = null): void
    {
        $this->validateXMLString((new Xml($xmlPath))->getContents(), $xsdPath);
    }

    public function validateXMLString(string $xml, ?string $xsdPath = null): void
    {
        self::checkEmptyWhenTrimmed($xml);

        // Resolve the schema up front so a missing XSD reports as FileDoesNotExist
        // rather than surfacing as a libxml parse error about the document.
        if ($xsdPath !== null) {
            $xsdPath = (new Xsd($xsdPath))->getPath();
        }

        // libxml error handling is process-global, so put it back the way we
        // found it instead of leaving every later consumer with it switched on.
        $previous = libxml_use_internal_errors(true);

        try {
            $document = new DOMDocument($this->version, $this->encoding);
            $document->loadXML($xml);

            if ($xsdPath !== null) {
                $document->schemaValidate($xsdPath);
            }

            self::parseErrors(libxml_get_errors());
        } finally {
            libxml_clear_errors();
            libxml_use_internal_errors($previous);
        }
    }

    /**
     * @throws InvalidXml
     */
    private static function checkEmptyWhenTrimmed(string $xmlContent): void
    {
        if (trim($xmlContent) === '') {
            throw new InvalidXml(ErrorMessages::XML_EMPTY_TRIMMED);
        }
    }

    /**
     * @param list<LibXMLError> $errors
     *
     * @throws InvalidXml
     */
    private static function parseErrors(array $errors): void
    {
        if ($errors === []) {
            return;
        }

        $messages = array_map(
            static fn (LibXMLError $error): string => trim($error->message),
            $errors
        );

        throw new InvalidXml(implode(Strings::NEW_LINE, $messages), $errors);
    }

    public function getVersion(): string
    {
        return $this->version;
    }

    public function setVersion(string $version): void
    {
        $this->version = $version;
    }

    public function getEncoding(): string
    {
        return $this->encoding;
    }

    public function setEncoding(string $encoding): void
    {
        $this->encoding = $encoding;
    }
}
