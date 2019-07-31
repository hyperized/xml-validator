<?php declare(strict_types=1);

namespace Hyperized\Xml\Constants;

/**
 * Class ErrorMessages
 *
 * @package Hyperized\Xml\Constants
 */
final class ErrorMessages
{
    public const XML_EMPTY_TRIMMED = 'The provided XML content is, after trimming, in fact an empty string';
    public const XML_NO_NAME = 'xmlParseEntityRef: no name';
    public const NO_FILE_CONTENTS = 'Could not get file contents';
}
