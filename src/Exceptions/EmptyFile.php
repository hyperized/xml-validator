<?php

declare(strict_types=1);

namespace Hyperized\Xml\Exceptions;

use RuntimeException;

/**
 * The file exists and is readable, but holds nothing.
 */
final class EmptyFile extends RuntimeException implements XmlValidatorException
{
}
