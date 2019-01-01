<?php declare(strict_types=1);

namespace Hyperized\Xml;

use DOMDocument;
use Hyperized\Xml\Constants\ErrorMessages;
use Hyperized\Xml\Constants\Strings;
use Hyperized\Xml\Exceptions\InvalidXml;

/**
 * Class Validator
 *
 * @package Hyperized\Xml
 * Based on: http://stackoverflow.com/a/30058598/1757763
 */
final class Validator
{
    /**
     * @var string
     */
    private $version = Strings::VERSION;
    /**
     * @var string
     */
    private $encoding = Strings::UTF_8;

    /**
     * @param  string $xmlFilename
     * @param  string $xsdFile
     * @return bool
     * @throws InvalidXml
     */
    public function isXMLFileValid(
        string $xmlFilename,
        string $xsdFile = null
    ): bool {
        return $this->isXMLStringValid(file_get_contents($xmlFilename), $xsdFile);
    }

    /**
     * @param  string $xml
     * @param  string $xsdFile
     * @return bool
     * @throws InvalidXml
     */
    public function isXMLStringValid(
        string $xml,
        string $xsdFile = null
    ): bool {
        if (\is_string($xsdFile)) {
            return $this->isXMLValid($xml, $xsdFile);
        }
        return $this->isXMLValid($xml);
    }

    /**
     * @param string      $xmlContent
     * @param string|null $xsdFile
     *
     * @return bool
     * @throws InvalidXml
     */
    private function isXMLValid(
        string $xmlContent,
        string $xsdFile = null
    ): bool {
        if (trim($xmlContent) === '') {
            throw new InvalidXml(ErrorMessages::XML_EMPTY_TRIMMED);
        }

        libxml_use_internal_errors(true);

        $document = new DOMDocument($this->version, $this->encoding);
        $document->loadXML($xmlContent);
        if (\is_string($xsdFile)) {
            $document->schemaValidate($xsdFile);
        }

        $errors = libxml_get_errors();
        libxml_clear_errors();
        if (!empty($errors)) {
            $return = [];
            foreach ($errors as $error) {
                $return[] = trim($error->message);
            }
            throw new InvalidXml(implode(Strings::NEW_LINE, $return));
        }
        return true;
    }

    /**
     * @param string $version
     */
    public function setVersion(string $version): void
    {
        $this->version = $version;
    }

    /**
     * @param string $encoding
     */
    public function setEncoding(string $encoding): void
    {
        $this->encoding = $encoding;
    }

    /**
     * @return string
     */
    public function getVersion(): string
    {
        return $this->version;
    }

    /**
     * @return string
     */
    public function getEncoding(): string
    {
        return $this->encoding;
    }
}
