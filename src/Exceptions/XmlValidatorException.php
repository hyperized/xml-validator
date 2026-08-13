<?php

declare(strict_types=1);

namespace Hyperized\Xml\Exceptions;

use Throwable;

/**
 * Implemented by every exception this package throws.
 *
 * Catch this to handle any validation failure without naming each concrete
 * class, and without the union growing every time one is added.
 */
interface XmlValidatorException extends Throwable
{
}
