<?php

declare(strict_types=1);

namespace Hyperized\Xml\Tests;

use Hyperized\Xml\Exceptions\EmptyFile;
use Hyperized\Xml\Exceptions\FileCouldNotBeOpenedException;
use Hyperized\Xml\Exceptions\FileDoesNotExist;
use Hyperized\Xml\Exceptions\InvalidXml;
use Hyperized\Xml\Exceptions\XmlValidatorException;
use Hyperized\Xml\Types\File;
use Hyperized\Xml\Types\Files\Xml;
use Hyperized\Xml\Types\Files\Xsd;
use Hyperized\Xml\Validator;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\UsesClass;
use RuntimeException;

#[CoversClass(InvalidXml::class)]
#[CoversClass(EmptyFile::class)]
#[CoversClass(FileCouldNotBeOpenedException::class)]
#[CoversClass(FileDoesNotExist::class)]
#[UsesClass(Validator::class)]
#[UsesClass(File::class)]
#[UsesClass(Xml::class)]
#[UsesClass(Xsd::class)]
final class ExceptionTest extends TestCase
{
    /**
     * @return iterable<string, array{class-string<XmlValidatorException>}>
     */
    public static function exceptionClasses(): iterable
    {
        yield 'InvalidXml' => [InvalidXml::class];
        yield 'EmptyFile' => [EmptyFile::class];
        yield 'FileDoesNotExist' => [FileDoesNotExist::class];
        yield 'FileCouldNotBeOpenedException' => [FileCouldNotBeOpenedException::class];
    }

    /**
     * @param class-string<XmlValidatorException> $class
     */
    #[DataProvider('exceptionClasses')]
    public function testEveryExceptionCarriesTheMarkerInterface(string $class): void
    {
        $exception = new $class('boom');

        self::assertInstanceOf(XmlValidatorException::class, $exception);
        self::assertInstanceOf(RuntimeException::class, $exception);
        self::assertSame('boom', $exception->getMessage());
    }

    /**
     * One catch block has to be enough, otherwise consumers write a union that
     * silently stops covering everything the day a fifth exception is added.
     */
    public function testTheMarkerInterfaceCatchesEveryFailureMode(): void
    {
        $validator = new Validator();

        /** @var array<string, callable(): void> $failures */
        $failures = [
            'missing file' => static function () use ($validator): void {
                $validator->validateXMLFile(self::fixturePath('does_not_exist.xml'));
            },
            'empty file' => static function () use ($validator): void {
                $validator->validateXMLFile(self::fixturePath('empty.xml'));
            },
            'malformed xml' => static function () use ($validator): void {
                $validator->validateXMLString(self::fixture('incorrect.xml'));
            },
            'missing schema' => static function () use ($validator): void {
                $validator->validateXMLString(
                    self::fixture('correct.xml'),
                    self::fixturePath('does_not_exist.xsd')
                );
            },
            'schema violation' => static function () use ($validator): void {
                $validator->validateXMLFile(
                    self::fixturePath('schema-violation.xml'),
                    self::fixturePath('simple.xsd')
                );
            },
        ];

        $caught = [];

        foreach ($failures as $label => $trigger) {
            try {
                $trigger();
                self::fail($label . ' should have thrown');
            } catch (XmlValidatorException $exception) {
                $caught[$label] = $exception::class;
            }
        }

        self::assertSame([
            'missing file' => FileDoesNotExist::class,
            'empty file' => EmptyFile::class,
            'malformed xml' => InvalidXml::class,
            'missing schema' => FileDoesNotExist::class,
            'schema violation' => InvalidXml::class,
        ], $caught);
    }

    /**
     * The message is a summary for logs. Anything that needs to point at a
     * location reads getErrors() instead.
     */
    public function testInvalidXmlKeepsTheLibxmlErrorsIntact(): void
    {
        $validator = new Validator();

        try {
            $validator->validateXMLString(self::fixture('incorrect.xml'));
            self::fail('Malformed XML should have thrown');
        } catch (InvalidXml $exception) {
            self::assertNotEmpty($exception->getErrors());

            $first = $exception->getErrors()[0];
            self::assertGreaterThan(0, $first->line, 'libxml line number should survive');
            self::assertStringContainsString(trim($first->message), $exception->getMessage());
        }
    }

    public function testInvalidXmlReportsEveryLibxmlErrorInTheMessage(): void
    {
        $validator = new Validator();

        try {
            $validator->validateXMLFile(
                self::fixturePath('schema-violation.xml'),
                self::fixturePath('simple.xsd')
            );
            self::fail('Schema violation should have thrown');
        } catch (InvalidXml $exception) {
            foreach ($exception->getErrors() as $error) {
                self::assertStringContainsString(trim($error->message), $exception->getMessage());
            }
        }
    }

    /**
     * Failures raised by this package rather than libxml have nothing to list.
     */
    public function testInvalidXmlDefaultsToNoLibxmlErrors(): void
    {
        $exception = new InvalidXml('raised without libxml');

        self::assertSame([], $exception->getErrors());
    }
}
