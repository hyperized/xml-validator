<?php declare(strict_types=1);

namespace Hyperized\Xml\Constants;

/**
 * Class ErrorMessages
 * @package Hyperized\Xml\Constants
 */
final class ErrorMessages
{
    public const XmlInvalidWithXsd = 'Could not validate XML string with XSD file';
    public const XmlInvalid = 'Could not validate XML string';
    public const XmlEmptyTrimmed = 'The provided XML content is, after trimming, in fact an empty string';
    public const XmlNoName = "xmlParseEntityRef: no name\n";
}