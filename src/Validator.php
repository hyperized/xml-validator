<?php

namespace Hyperized\Xml;

use Hyperized\Xml\Exceptions\InvalidXmlException;

/**
 * Class Validator
 * @package Hyperized\Xml
 * Based on: http://stackoverflow.com/a/30058598/1757763
 */
final class Validator
{
    /**
     * @var array
     */
    private $errors;

    /**
     * @param        $xmlFilename
     * @param        $xsdFile
     * @param string $version
     * @param string $encoding
     *
     * @return bool
     * @throws InvalidXmlException
     */
    public function isXMLFileValid(string $xmlFilename, string $xsdFile = null, string $version = '1.0', string $encoding = 'utf-8'): bool
    {
        return $this->isXMLStringValid(file_get_contents($xmlFilename), $xsdFile, $version, $encoding);
    }

    /**
     * @param        $xml
     * @param        $xsdFile
     * @param string $version
     * @param string $encoding
     *
     * @return bool
     * @throws InvalidXmlException
     */
    public function isXMLStringValid(string $xml, string $xsdFile = null, string $version = '1.0', string $encoding = 'utf-8'): bool
    {
        if ($xsdFile !== null && !$this->isXMLContentValid($xml, $version, $encoding, $xsdFile)) {
            throw new InvalidXmlException('Could not validate XML string with XSD file');
        }
        if (!$this->isXMLContentValid($xml, $version, $encoding)) {
            throw new InvalidXmlException('Could not validate XML string');
        }
        return true;
    }

    /**
     * @param string $xmlContent
     * @param string $version
     * @param string $encoding
     * @param string|null $xsdFile
     *
     * @return bool
     * @throws InvalidXmlException
     */
    private function isXMLContentValid(string $xmlContent, string $version = '1.0', string $encoding = 'utf-8', string $xsdFile = null): bool
    {
        if (trim($xmlContent) === '') {
            throw new InvalidXmlException('The provided XML content is, after trimming, in fact an empty string');
        }

        libxml_use_internal_errors(true);

        $doc = new \DOMDocument($version, $encoding);
        $doc->loadXML($xmlContent);
        if ($xsdFile !== null) {
            $doc->schemaValidate($xsdFile);
        }
        $this->errors = libxml_get_errors();
        libxml_clear_errors();

        return empty($this->errors);
    }

    /**
     * @return mixed
     */
    public function getErrors(): array
    {
        return $this->errors;
    }

    /**
     * @return string
     */
    public function getPrettyErrors(): string
    {
        $return = [];
        foreach ($this->errors as $error) {
            $return[] = $error->message;
        }
        return implode("\n", $return);
    }
}
