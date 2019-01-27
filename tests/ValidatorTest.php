<?php declare(strict_types=1);

namespace Hyperized\Xml\Tests;

use Hyperized\Xml\Constants\ErrorMessages;
use Hyperized\Xml\Constants\Strings;
use Hyperized\Xml\Exceptions\FileCouldNotBeOpenedException;
use Hyperized\Xml\Exceptions\InvalidXml;
use Hyperized\Xml\Validator;
use PHPUnit\Framework\TestCase;

/**
 * Class ValidatorTest
 *
 * @package Hyperized\Xml\Validator\Tests
 */
final class ValidatorTest extends TestCase
{
    /**
     * @var string
     */
    private static $xsdFile = __DIR__ . '/files/simple.xsd';
    /**
     * @var string
     */
    private static $xmlFile = __DIR__ . '/files/correct.xml';
    /**
     * @var string
     */
    private static $incorrectXmlFile = __DIR__ . '/files/incorrect.xml';
    /**
     * @var string
     */
    private static $nonExistentFile = __DIR__ . '/files/does_not_exist.xml';
    /**
     * @var string
     */
    private static $version = Strings::VERSION;
    /**
     * @var string
     */
    private static $encoding = Strings::UTF_8;
    /**
     * @var Validator
     */
    private $validator;


    public function setUp(): void
    {
        $this->validator = new Validator();
    }

    public function testVersion(): void
    {
        $this->validator->setVersion(static::$version);
        self::assertEquals(static::$version, $this->validator->getVersion());
    }

    public function testEncoding(): void
    {
        $this->validator->setEncoding(static::$encoding);
        self::assertEquals(static::$encoding, $this->validator->getEncoding());
    }

    /**
     * @throws FileCouldNotBeOpenedException
     * @throws InvalidXml
     */
    public function testValidXMLFile(): void
    {
        self::assertTrue($this->validator->isXMLFileValid(static::$xmlFile));
    }

    /**
     * @throws InvalidXml
     */
    public function testValidXMLString(): void
    {
        $contents = file_get_contents(static::$xmlFile);
        if (\is_string($contents)) {
            self::assertTrue($this->validator->isXMLStringValid($contents));
        }
    }

    /**
     * @throws InvalidXml
     */
    public function testInvalidXMLString(): void
    {
        $this->expectException(InvalidXml::class);
        $this->expectExceptionMessage(ErrorMessages::XML_NO_NAME);
        $contents = file_get_contents(static::$incorrectXmlFile);
        if(\is_string($contents)) {
            $this->validator->isXMLStringValid($contents);
        }
    }

    /**
     * @throws InvalidXml
     */
    public function testEmptyXMLString(): void
    {
        $this->expectException(InvalidXml::class);
        $this->expectExceptionMessage(ErrorMessages::XML_EMPTY_TRIMMED);
        $this->validator->isXMLStringValid('');
    }

    /**
     * @throws FileCouldNotBeOpenedException
     * @throws InvalidXml
     */
    public function testValidXSDFile(): void
    {
        self::assertTrue($this->validator->isXMLFileValid(static::$xmlFile, static::$xsdFile));
    }

    /**
     * @throws FileCouldNotBeOpenedException
     * @throws InvalidXml
     */
    public function testInvalidXMLFile(): void
    {
        $this->expectException(InvalidXml::class);
        $this->expectExceptionMessage(ErrorMessages::XML_NO_NAME);
        $this->validator->isXMLFileValid(static::$incorrectXmlFile);
    }

    /**
     * @throws FileCouldNotBeOpenedException
     * @throws InvalidXml
     */
    public function testInvalidXSDFile(): void
    {
        $this->expectException(InvalidXml::class);
        $this->expectExceptionMessage(ErrorMessages::XML_NO_NAME);
        $this->validator->isXMLFileValid(static::$incorrectXmlFile, static::$xsdFile);
    }

    /**
     * @throws FileCouldNotBeOpenedException
     * @throws InvalidXml
     */
    public function testEmptyXmlFile(): void
    {
        $this->expectException(FileCouldNotBeOpenedException::class);
        $this->expectExceptionMessage(ErrorMessages::NO_FILE_CONTENTS);
        $this->validator->isXMLFileValid(static::$nonExistentFile);
    }
}
