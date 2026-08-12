<?php

declare(strict_types=1);

namespace Hyperized\Xml\Exceptions;

use LibXMLError;
use RuntimeException;

/**
 * The document is not well formed, or does not satisfy the schema it was
 * checked against.
 *
 * The message is a newline-joined summary for logging. Anything that needs to
 * point at a location should read getErrors() instead, which keeps libxml's
 * line, column and level intact.
 */
final class InvalidXml extends RuntimeException implements XmlValidatorException
{
    /**
     * @param list<LibXMLError> $errors Empty when the failure came from this
     *                                  package rather than from libxml.
     */
    public function __construct(string $message, private readonly array $errors = [])
    {
        parent::__construct($message);
    }

    /**
     * @return list<LibXMLError>
     */
    public function getErrors(): array
    {
        return $this->errors;
    }
}
