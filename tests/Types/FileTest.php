<?php

declare(strict_types=1);

namespace Hyperized\Xml\Tests\Types;

use Hyperized\Xml\Constants\ErrorMessages;
use Hyperized\Xml\Exceptions\EmptyFile;
use Hyperized\Xml\Exceptions\FileCouldNotBeOpenedException;
use Hyperized\Xml\Exceptions\FileDoesNotExist;
use Hyperized\Xml\Tests\InvalidStreamWrapper;
use Hyperized\Xml\Tests\TestCase;
use Hyperized\Xml\Types\File;
use Hyperized\Xml\Types\Files\Xml;
use Hyperized\Xml\Types\Files\Xsd;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\UsesClass;

#[CoversClass(File::class)]
#[CoversClass(Xml::class)]
#[CoversClass(Xsd::class)]
#[UsesClass(EmptyFile::class)]
#[UsesClass(FileDoesNotExist::class)]
#[UsesClass(FileCouldNotBeOpenedException::class)]
final class FileTest extends TestCase
{
    protected function tearDown(): void
    {
        InvalidStreamWrapper::unregister();
    }

    public function testExposesThePathItWasGiven(): void
    {
        $path = self::fixturePath('correct.xml');

        self::assertSame($path, (new File($path))->getPath());
    }

    public function testReadsTheContentsOfAnExistingFile(): void
    {
        $file = new File(self::fixturePath('correct.xml'));

        self::assertStringContainsString('This is better xml', $file->getContents());
    }

    public function testRejectsAPathThatDoesNotExist(): void
    {
        $this->expectException(FileDoesNotExist::class);
        $this->expectExceptionMessage(ErrorMessages::FILE_DOES_NOT_EXIST);

        new File(self::fixturePath('does_not_exist.xml'));
    }

    public function testRejectsAnEmptyFile(): void
    {
        $file = new File(self::fixturePath('empty.xml'));

        $this->expectException(EmptyFile::class);
        $this->expectExceptionMessage(ErrorMessages::EMPTY_FILE);

        $file->getContents();
    }

    /**
     * Reachable only when a path passes file_exists() but refuses to open,
     * which is what the stream wrapper arranges.
     */
    public function testRejectsAFileThatCannotBeOpened(): void
    {
        InvalidStreamWrapper::register();

        $file = new File(InvalidStreamWrapper::SCHEME . '://unreadable');

        $this->expectException(FileCouldNotBeOpenedException::class);
        $this->expectExceptionMessage(ErrorMessages::FILE_COULD_NOT_BE_OPENED);

        @$file->getContents();
    }

    /**
     * @return iterable<string, array{class-string<File>, string}>
     */
    public static function fileTypes(): iterable
    {
        yield 'Xml' => [Xml::class, 'correct.xml'];
        yield 'Xsd' => [Xsd::class, 'simple.xsd'];
    }

    /**
     * @param class-string<File> $class
     */
    #[DataProvider('fileTypes')]
    public function testTypedFilesBehaveLikeTheBaseFile(string $class, string $fixture): void
    {
        $file = new $class(self::fixturePath($fixture));

        self::assertInstanceOf(File::class, $file);
        self::assertSame(self::fixturePath($fixture), $file->getPath());
        self::assertNotSame('', $file->getContents());
    }
}
