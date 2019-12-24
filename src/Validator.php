<?php declare(strict_types=1);

namespace Hyperized\Xml;

use DOMDocument;
use Hyperized\Xml\Constants\ErrorMessages;
use Hyperized\Xml\Constants\Strings;
use Hyperized\Xml\Exceptions\FileCouldNotBeOpenedException;
use Hyperized\Xml\Exceptions\InvalidXml;
use Hyperized\Xml\Exceptions\EmptyFile;
use Hyperized\Xml\Exceptions\FileDoesNotExist;
use Hyperized\Xml\Types\Files\Xml;
use Hyperized\Xml\Types\Files\Xsd;
use function is_string;
use LibXMLError;

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

    public function isXMLFileValid(string $xmlPath, string $xsdPath = null): bool
    {
        try {
            $string = (new Xml($xmlPath))
                ->getContents();
        } catch (EmptyFile $e) {
            return false;
        } catch (FileCouldNotBeOpenedException $e) {
            return false;
        } catch (FileDoesNotExist $e) {
            return false;
        }

        if ($xsdPath !== null) {
            try {
                $xsdPath = (new Xsd($xsdPath))
                    ->getPath();
            } catch (FileDoesNotExist $e) {
                return false;
            }
        }

        return $this->isXMLStringValid($string, $xsdPath);
    }

    /**
     * @param  string      $xml
     * @param  string|null $xsdPath
     * @return bool
     */
    public function isXMLStringValid(string $xml, string $xsdPath = null): bool
    {
        try {
            if (is_string($xsdPath)) {
                return $this->isXMLValid($xml, $xsdPath);
            }
            return $this->isXMLValid($xml);
        } catch (InvalidXml $e) {
            return false;
        }
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
     * @param  array<LibXMLError>|null $errors
     * @throws InvalidXml
     */
    private static function parseErrors(?array $errors): void
    {
        if (!empty($errors)) {
            $reduced = array_reduce(
                $errors,
                static function (
                    ?array $carry,
                    LibXMLError $item
                ): array {
                    $carry[] = trim($item->message);
                    return $carry;
                }
            );

            if (!empty($reduced)) {
                throw new InvalidXml(implode(Strings::NEW_LINE, $reduced));
            }
        }
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
