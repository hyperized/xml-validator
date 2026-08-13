<?php

declare(strict_types=1);

namespace Hyperized\Xml\Exceptions;

use RuntimeException;

/**
 * The path exists but reading it failed, for instance on a permission problem
 * or a stream wrapper that refuses to open.
 */
final class FileCouldNotBeOpenedException extends RuntimeException implements XmlValidatorException
{
}
