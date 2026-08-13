<?php

declare(strict_types=1);

namespace Hyperized\Xml\Constants;

/**
 * Class ErrorMessages
 *
 * @package Hyperized\Xml\Constants
 */
final class ErrorMessages
{
    public const XML_EMPTY_TRIMMED = 'The provided XML content is, after trimming, in fact an empty string';
    public const EMPTY_FILE = 'File is empty';
    public const FILE_DOES_NOT_EXIST = 'File does not exist';
    public const FILE_COULD_NOT_BE_OPENED = 'File could not be opened';
}
