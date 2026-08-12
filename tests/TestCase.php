<?php

declare(strict_types=1);

namespace Hyperized\Xml\Tests;

use PHPUnit\Framework\TestCase as PHPUnitTestCase;

abstract class TestCase extends PHPUnitTestCase
{
    protected const string FIXTURE_DIR = __DIR__ . '/files';

    final protected static function fixturePath(string $name): string
    {
        return self::FIXTURE_DIR . '/' . $name;
    }

    /**
     * Reads a fixture, failing the test if it cannot be read.
     *
     * Guarding the read with a conditional instead would let the test pass
     * without ever reaching its assertions.
     */
    final protected static function fixture(string $name): string
    {
        $path = self::fixturePath($name);
        $contents = file_get_contents($path);

        self::assertIsString($contents, 'Unable to read fixture: ' . $path);

        return $contents;
    }
}
