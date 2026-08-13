<?php

declare(strict_types=1);

namespace Hyperized\Xml\Tests;

use Hyperized\Xml\Constants\ErrorMessages;
use Hyperized\Xml\Constants\Strings;
use Hyperized\Xml\Exceptions\EmptyFile;
use Hyperized\Xml\Exceptions\FileCouldNotBeOpenedException;
use Hyperized\Xml\Exceptions\FileDoesNotExist;
use Hyperized\Xml\Exceptions\InvalidXml;
use Hyperized\Xml\Types\File;
use Hyperized\Xml\Types\Files\Xml;
use Hyperized\Xml\Types\Files\Xsd;
use Hyperized\Xml\Validator;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\UsesClass;

#[CoversClass(Validator::class)]
#[UsesClass(File::class)]
#[UsesClass(Xml::class)]
#[UsesClass(Xsd::class)]
#[UsesClass(InvalidXml::class)]
#[UsesClass(EmptyFile::class)]
#[UsesClass(FileDoesNotExist::class)]
#[UsesClass(FileCouldNotBeOpenedException::class)]
final class ValidatorTest extends TestCase
{
    private Validator $validator;

    protected function setUp(): void
    {
        $this->validator = new Validator();
    }

    protected function tearDown(): void
    {
        InvalidStreamWrapper::unregister();
    }

    /**
     * Configuration
     */
    public function testDefaultsToXmlVersionOneAndUtf8(): void
    {
        self::assertSame(Strings::VERSION, $this->validator->getVersion());
        self::assertSame(Strings::UTF_8, $this->validator->getEncoding());
    }

    public function testAcceptsVersionAndEncodingThroughTheConstructor(): void
    {
        $validator = new Validator('1.1', 'iso-8859-1');

        self::assertSame('1.1', $validator->getVersion());
        self::assertSame('iso-8859-1', $validator->getEncoding());
    }

    public function testVersionCanBeChanged(): void
    {
        $this->validator->setVersion('1.1');

        self::assertSame('1.1', $this->validator->getVersion());
    }

    public function testEncodingCanBeChanged(): void
    {
        $this->validator->setEncoding('iso-8859-1');

        self::assertSame('iso-8859-1', $this->validator->getEncoding());
    }

    /**
     * String predicates
     */
    public function testValidXmlStringIsValid(): void
    {
        self::assertTrue($this->validator->isXMLStringValid(self::fixture('correct.xml')));
    }

    public function testValidXmlStringIsValidAgainstItsSchema(): void
    {
        self::assertTrue($this->validator->isXMLStringValid(
            self::fixture('correct.xml'),
            self::fixturePath('simple.xsd')
        ));
    }

    public function testMalformedXmlStringIsNotValid(): void
    {
        self::assertFalse($this->validator->isXMLStringValid(self::fixture('incorrect.xml')));
    }

    public function testWellFormedXmlStringViolatingItsSchemaIsNotValid(): void
    {
        self::assertFalse($this->validator->isXMLStringValid(
            self::fixture('schema-violation.xml'),
            self::fixturePath('simple.xsd')
        ));
    }

    public function testEmptyXmlStringIsNotValid(): void
    {
        self::assertFalse($this->validator->isXMLStringValid(''));
    }

    public function testWhitespaceOnlyXmlStringIsNotValid(): void
    {
        self::assertFalse($this->validator->isXMLStringValid("  \n\t  "));
    }

    public function testXmlStringIsNotValidWhenTheSchemaIsMissing(): void
    {
        self::assertFalse($this->validator->isXMLStringValid(
            self::fixture('correct.xml'),
            self::fixturePath('does_not_exist.xsd')
        ));
    }

    /**
     * File predicates
     */
    public function testValidXmlFileIsValid(): void
    {
        self::assertTrue($this->validator->isXMLFileValid(self::fixturePath('correct.xml')));
    }

    public function testValidXmlFileIsValidAgainstItsSchema(): void
    {
        self::assertTrue($this->validator->isXMLFileValid(
            self::fixturePath('correct.xml'),
            self::fixturePath('simple.xsd')
        ));
    }

    public function testMalformedXmlFileIsNotValid(): void
    {
        self::assertFalse($this->validator->isXMLFileValid(self::fixturePath('incorrect.xml')));
    }

    public function testMissingXmlFileIsNotValid(): void
    {
        self::assertFalse($this->validator->isXMLFileValid(self::fixturePath('does_not_exist.xml')));
    }

    public function testEmptyXmlFileIsNotValid(): void
    {
        self::assertFalse($this->validator->isXMLFileValid(self::fixturePath('empty.xml')));
    }

    public function testUnreadableXmlFileIsNotValid(): void
    {
        InvalidStreamWrapper::register();

        self::assertFalse(@$this->validator->isXMLFileValid(InvalidStreamWrapper::SCHEME . '://unreadable'));
    }

    public function testXmlFileIsNotValidWhenTheSchemaIsMissing(): void
    {
        self::assertFalse($this->validator->isXMLFileValid(
            self::fixturePath('correct.xml'),
            self::fixturePath('does_not_exist.xsd')
        ));
    }

    /**
     * Throwing variants report why
     */
    public function testValidateXmlStringReturnsQuietlyForValidXml(): void
    {
        $xml = self::fixture('correct.xml');

        $this->validator->validateXMLString($xml);

        self::assertTrue($this->validator->isXMLStringValid($xml));
    }

    public function testValidateXmlStringRejectsMalformedXml(): void
    {
        $this->expectException(InvalidXml::class);

        $this->validator->validateXMLString(self::fixture('incorrect.xml'));
    }

    public function testValidateXmlStringRejectsAnEmptyDocument(): void
    {
        $this->expectException(InvalidXml::class);
        $this->expectExceptionMessage(ErrorMessages::XML_EMPTY_TRIMMED);

        $this->validator->validateXMLString('   ');
    }

    public function testValidateXmlStringReportsAMissingSchemaAsAMissingFile(): void
    {
        $this->expectException(FileDoesNotExist::class);
        $this->expectExceptionMessage(ErrorMessages::FILE_DOES_NOT_EXIST);

        $this->validator->validateXMLString(
            self::fixture('correct.xml'),
            self::fixturePath('does_not_exist.xsd')
        );
    }

    public function testValidateXmlFileReturnsQuietlyForValidXml(): void
    {
        $path = self::fixturePath('correct.xml');

        $this->validator->validateXMLFile($path, self::fixturePath('simple.xsd'));

        self::assertTrue($this->validator->isXMLFileValid($path));
    }

    public function testValidateXmlFileRejectsAMissingFile(): void
    {
        $this->expectException(FileDoesNotExist::class);
        $this->expectExceptionMessage(ErrorMessages::FILE_DOES_NOT_EXIST);

        $this->validator->validateXMLFile(self::fixturePath('does_not_exist.xml'));
    }

    public function testValidateXmlFileRejectsAnEmptyFile(): void
    {
        $this->expectException(EmptyFile::class);
        $this->expectExceptionMessage(ErrorMessages::EMPTY_FILE);

        $this->validator->validateXMLFile(self::fixturePath('empty.xml'));
    }

    public function testValidateXmlFileRejectsAnUnreadableFile(): void
    {
        InvalidStreamWrapper::register();

        $this->expectException(FileCouldNotBeOpenedException::class);
        $this->expectExceptionMessage(ErrorMessages::FILE_COULD_NOT_BE_OPENED);

        @$this->validator->validateXMLFile(InvalidStreamWrapper::SCHEME . '://unreadable');
    }

    public function testValidateXmlFileRejectsASchemaViolation(): void
    {
        $this->expectException(InvalidXml::class);

        $this->validator->validateXMLFile(
            self::fixturePath('schema-violation.xml'),
            self::fixturePath('simple.xsd')
        );
    }

    /**
     * The validator keeps no error state, so an instance stays reusable and
     * shareable. It also leaves libxml's process-global settings alone.
     */
    public function testAFailedCallDoesNotAffectTheNextOne(): void
    {
        self::assertFalse($this->validator->isXMLStringValid(self::fixture('incorrect.xml')));
        self::assertTrue($this->validator->isXMLStringValid(self::fixture('correct.xml')));
        self::assertFalse($this->validator->isXMLFileValid(self::fixturePath('does_not_exist.xml')));
        self::assertTrue($this->validator->isXMLFileValid(self::fixturePath('correct.xml')));
    }

    public function testLibxmlInternalErrorHandlingIsRestoredAfterSuccess(): void
    {
        $before = libxml_use_internal_errors();

        $this->validator->validateXMLString(self::fixture('correct.xml'));

        self::assertSame($before, libxml_use_internal_errors());
    }

    public function testLibxmlInternalErrorHandlingIsRestoredAfterFailure(): void
    {
        $before = libxml_use_internal_errors();

        self::assertFalse($this->validator->isXMLStringValid(self::fixture('incorrect.xml')));
        self::assertSame($before, libxml_use_internal_errors());
    }

    public function testLibxmlInternalErrorHandlingIsRestoredWhenAlreadyEnabled(): void
    {
        $before = libxml_use_internal_errors(true);

        try {
            self::assertFalse($this->validator->isXMLStringValid(self::fixture('incorrect.xml')));
            self::assertTrue(libxml_use_internal_errors());
        } finally {
            libxml_use_internal_errors($before);
        }
    }
}
