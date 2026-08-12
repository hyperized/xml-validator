<?php

declare(strict_types=1);

namespace Hyperized\Xml\Tests;

/**
 * A stream wrapper whose paths pass file_exists() but refuse to open.
 *
 * That combination is the only way to reach the branch where
 * file_get_contents() returns false on a path the constructor already accepted.
 */
final class InvalidStreamWrapper
{
    public const string SCHEME = 'invalid';

    /** @var resource|null */
    public $context;

    public static function register(): void
    {
        if (in_array(self::SCHEME, stream_get_wrappers(), true)) {
            stream_wrapper_unregister(self::SCHEME);
        }

        stream_wrapper_register(self::SCHEME, self::class);
    }

    public static function unregister(): void
    {
        if (in_array(self::SCHEME, stream_get_wrappers(), true)) {
            stream_wrapper_unregister(self::SCHEME);
        }
    }

    public function stream_open(string $path, string $mode, int $options, ?string &$openedPath): bool
    {
        return false;
    }

    /**
     * Reported as a regular file so file_exists() succeeds and the failure
     * surfaces on the read instead.
     *
     * @return array<string, int>
     */
    public function url_stat(string $path, int $flags): array
    {
        return ['mode' => 0100644, 'size' => 1];
    }
}
