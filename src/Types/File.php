<?php declare(strict_types=1);

namespace Hyperized\Xml\Types;

use Hyperized\Xml\Constants\ErrorMessages;
use Hyperized\Xml\Exceptions\EmptyFile;
use Hyperized\Xml\Exceptions\FileCouldNotBeOpenedException;
use Hyperized\Xml\Exceptions\FileDoesNotExist;

/**
 * Class File
 *
 * @package Hyperized\Xml\Types\Xml
 */
class File
{
    /**
     * @var string
     */
    private $path;

    /**
     * File constructor.
     *
     * @param  string $path
     * @throws FileDoesNotExist
     */
    public function __construct(string $path)
    {
        $this->path = $path;
        $this->pathExists();
    }

    /**
     * @return bool
     * @throws FileDoesNotExist
     */
    private function pathExists(): bool
    {
        if (!file_exists($this->path)) {
            throw new FileDoesNotExist(ErrorMessages::FILE_DOES_NOT_EXIST);
        }
        return true;
    }

    /**
     * @return string
     * @throws EmptyFile
     * @throws FileCouldNotBeOpenedException
     */
    public function getContents(): string
    {
        $contents = @file_get_contents($this->path);

        if ($contents === false) {
            throw new FileCouldNotBeOpenedException(ErrorMessages::FILE_COULD_NOT_BE_OPENED);
        }

        if ($contents === '') {
            throw new EmptyFile(ErrorMessages::EMPTY_FILE);
        }

        return $contents;
    }

    /**
     * @return string
     */
    public function getPath(): string
    {
        return $this->path;
    }
}
