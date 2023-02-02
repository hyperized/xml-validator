<?php

declare(strict_types=1);

namespace Hyperized\Xml;

/**
 * Interface ValidatorInterface
 *
 * @package Hyperized\Xml
 */
interface ValidatorInterface
{
    /**
     * @param  string      $xmlPath
     * @param  string|null $xsdPath
     * @return bool
     */
    public function isXMLFileValid(string $xmlPath, string $xsdPath = null, bool $returnError = false): bool|string

    /**
     * @param  string      $xml
     * @param  string|null $xsdPath
     * @param  bool $returnError
     * @return bool
     */
    public function isXMLStringValid(string $xml, string $xsdPath = null, bool $returnError = false): bool|string;

    /**
     * @return string
     */
    public function getVersion(): string;

    /**
     * @param string $version
     */
    public function setVersion(string $version): void;

    /**
     * @return string
     */
    public function getEncoding(): string;

    /**
     * @param string $encoding
     */
    public function setEncoding(string $encoding): void;
}
