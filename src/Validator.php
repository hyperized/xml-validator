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
     * @var
     */
    protected $errors;

    /**
     * @param        $xmlFilename
     * @param null   $xsdFile
     * @param string $version
     * @param string $encoding
     *
     * @return bool
     */
    public function isXMLFileValid($xmlFilename, $xsdFile = null, $version = '1.0', $encoding = 'utf-8')
    {
        $xmlContent = file_get_contents($xmlFilename);
        $this->isXMLStringValid($xmlContent, $xsdFile, $version, $encoding);
        return true;
    }

    /**
     * @param        $xml
     * @param null   $xsdFile
     * @param string $version
     * @param string $encoding
     *
     * @return bool
     * @throws \Exception
     */
    public function isXMLStringValid($xml, $xsdFile = null, $version = '1.0', $encoding = 'utf-8')
    {
        if ($xsdFile !== null) {
            if (!$this->isXMLContentValid($xml, $version, $encoding, $xsdFile)) {
                throw new \Exception('XSD validation failed!');
            }
        }
        if (!$this->isXMLContentValid($xml, $version, $encoding)) {
            throw new \Exception('XML content validation failed!');
        }
        return true;
    }

    /**
     * @param        $xmlContent
     * @param string $version
     * @param string $encoding
     * @param null   $xsdFile
     *
     * @return bool
     */
    public function isXMLContentValid($xmlContent, $version = '1.0', $encoding = 'utf-8', $xsdFile = null)
    {
        if (trim($xmlContent) == '') {
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
    public function getErrors()
    {
        return $this->errors;
    }

    /**
     * @return string
     */
    public function getPrettyErrors()
    {
        $return = [];
        foreach ($this->errors as $error) {
            $return[] = $error->message;
        }
        return implode("\n", $return);
    }
}