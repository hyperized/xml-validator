<?php declare(strict_types=1);

namespace Hyperized\Xml\Tests;

/**
 * Class InvalidStreamWrapper
 *
 * @package Hyperized\Xml\Types
 */
class InvalidStreamWrapper
{
    /**
     * @return bool
     */
    public function stream_open(): bool
    {
        return false;
    }

    /**
     * @return array<string>
     */
    public function url_stat(): array
    {
        return [];
    }
}
