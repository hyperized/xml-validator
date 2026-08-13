<?php

declare(strict_types=1);

namespace Hyperized\Xml\Exceptions;

use RuntimeException;

/**
 * No file at the given path.
 */
final class FileDoesNotExist extends RuntimeException implements XmlValidatorException
{
}
