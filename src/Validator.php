<?php

namespace Hyperized\Xml;

/**
 * Class Validator
 * @package Hyperized\Xml
 * Based on: http://stackoverflow.com/a/30058598/1757763
 */
class Validator
{
    /**
     * @var array
     */
    protected $errors;

    /**
     * @param        $xmlFilename
     * @param        $xsdFile
     * @param string $version
     * @param string $encoding
     *
     * @return bool
     */
    public function isXMLFileValid($xmlFilename, $xsdFile = null, $version = '1.0', $encoding = 'utf-8'): bool
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
     */
    public function isXMLStringValid($xml, $xsdFile = null, $version = '1.0', $encoding = 'utf-8'): bool
    {
        if ($xsdFile !== null && !$this->isXMLContentValid($xml, $version, $encoding, $xsdFile)) {
            return false;
        }
        if (!$this->isXMLContentValid($xml, $version, $encoding)) {
            return false;
        }
        return true;
    }

    /**
     * @param        $xmlContent
     * @param string $version
     * @param string $encoding
     * @param        $xsdFile
     *
     * @return bool
     */
    public function isXMLContentValid($xmlContent, $version = '1.0', $encoding = 'utf-8', $xsdFile = null): bool
    {
        if (trim($xmlContent) === '') {
            return false;
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
