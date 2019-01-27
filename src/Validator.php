<?php declare(strict_types=1);

namespace Hyperized\Xml;

use DOMDocument;
use Exception;
use Hyperized\Xml\Constants\ErrorMessages;
use Hyperized\Xml\Constants\Strings;
use Hyperized\Xml\Exceptions\FileCouldNotBeOpenedException;
use Hyperized\Xml\Exceptions\InvalidXml;

/**
 * Class Validator
 *
 * @package Hyperized\Xml
 * Based on: http://stackoverflow.com/a/30058598/1757763
 */
final class Validator implements ValidatorInterface
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
     * @param  string      $xmlPath
     * @param  string|null $xsdPath
     * @return bool
     * @throws FileCouldNotBeOpenedException
     * @throws InvalidXml
     */
    public function isXMLFileValid(string $xmlPath, string $xsdPath = null): bool
    {
        return $this->isXMLStringValid(self::getFileContent($xmlPath), $xsdPath);
    }

    /**
     * @param  string      $xml
     * @param  string|null $xsdPath
     * @return bool
     * @throws InvalidXml
     */
    public function isXMLStringValid(string $xml, string $xsdPath = null): bool
    {
        if (\is_string($xsdPath)) {
            return $this->isXMLValid($xml, $xsdPath);
        }
        return $this->isXMLValid($xml);
    }

    /**
     * @param  string      $xmlContent
     * @param  string|null $xsdPath
     * @return bool
     * @throws InvalidXml
     */
    private function isXMLValid(string $xmlContent, string $xsdPath = null): bool
    {
        self::checkEmptyWhenTrimmed($xmlContent);

        libxml_use_internal_errors(true);

        $document = new DOMDocument($this->version, $this->encoding);
        $document->loadXML($xmlContent);
        if (isset($xsdPath)) {
            $document->schemaValidate($xsdPath);
        }

        $errors = libxml_get_errors();
        libxml_clear_errors();
        self::parseErrors($errors);
        return true;
    }

    /**
     * @param  string $xmlContent
     * @throws InvalidXml
     */
    private static function checkEmptyWhenTrimmed(string $xmlContent): void
    {
        if (trim($xmlContent) === '') {
            throw new InvalidXml(ErrorMessages::XML_EMPTY_TRIMMED);
        }
    }

    /**
     * @param  array $errors
     * @throws InvalidXml
     */
    private static function parseErrors(array $errors): void
    {
        if (!empty($errors)) {
            $return = [];
            foreach ($errors as $error) {
                $return[] = trim($error->message);
            }
            throw new InvalidXml(implode(Strings::NEW_LINE, $return));
        }
    }

    /**
     * @param  string $fileName
     * @return string
     * @throws FileCouldNotBeOpenedException
     */
    private static function getFileContent(string $fileName): string
    {
        try {
            $contents = file_get_contents($fileName);
        } catch (Exception $exception) {
            throw new FileCouldNotBeOpenedException(ErrorMessages::NO_FILE_CONTENTS);
        }
        return '' ?: $contents;
    }

    /**
     * @return string
     */
    public function getVersion(): string
    {
        return $this->version;
    }

    /**
     * @param string $version
     */
    public function setVersion(string $version): void
    {
        $this->version = $version;
    }

    /**
     * @return string
     */
    public function getEncoding(): string
    {
        return $this->encoding;
    }

    /**
     * @param string $encoding
     */
    public function setEncoding(string $encoding): void
    {
        $this->encoding = $encoding;
    }
}
