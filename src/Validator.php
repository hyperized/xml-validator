<?php declare(strict_types=1);

namespace Hyperized\Xml;

use Hyperized\Xml\Constants\Strings;
use Hyperized\Xml\Exceptions\InvalidXmlException;
use Hyperized\Xml\Constants\ErrorMessages;

/**
 * Class Validator
 * @package Hyperized\Xml
 * Based on: http://stackoverflow.com/a/30058598/1757763
 */
final class Validator
{
    /**
     * @param        $xmlFilename
     * @param        $xsdFile
     * @param string $version
     * @param string $encoding
     *
     * @return bool
     * @throws InvalidXmlException
     */
    public function isXMLFileValid(string $xmlFilename, string $xsdFile = null, string $version = Strings::version, string $encoding = Strings::UTF8): bool
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
    public function isXMLStringValid(string $xml, string $xsdFile = null, string $version = Strings::version, string $encoding = Strings::UTF8): bool
    {
        if ($xsdFile !== null) {
            return $this->isXMLContentValid($xml, $version, $encoding, $xsdFile);
        }
        return $this->isXMLContentValid($xml, $version, $encoding);
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
    private function isXMLContentValid(string $xmlContent, string $version = Strings::version, string $encoding = Strings::UTF8, string $xsdFile = null): bool
    {
        if (trim($xmlContent) === '') {
            throw new InvalidXmlException(ErrorMessages::XmlEmptyTrimmed);
        }

        libxml_use_internal_errors(true);

        $doc = new \DOMDocument($version, $encoding);
        $doc->loadXML($xmlContent);
        if ($xsdFile !== null) {
            $doc->schemaValidate($xsdFile);
        }

        $errors = libxml_get_errors();
        libxml_clear_errors();
        if(!empty($errors))
        {
            $return = [];
            foreach ($errors as $error) {
                $return[] = trim($error->message);
            }
            throw new InvalidXmlException(implode(Strings::newLine, $return));
        }
        return true;
    }
}
