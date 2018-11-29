<?php declare(strict_types=1);

namespace Hyperized\Xml\Validator\Tests;

use Hyperized\Xml\Constants\ErrorMessages;
use Hyperized\Xml\Exceptions\InvalidXmlException;
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
     * @var Validator
     */
    private $validator;

    public function setUp(): void
    {
        $this->validator = new Validator();
    }

    public function testValidXMLFile(): void
    {
        self::assertTrue($this->validator->isXMLFileValid(static::$xmlFile));
    }

    public function testValidXMLString(): void
    {
        $xml = file_get_contents(static::$xmlFile);
        self::assertTrue($this->validator->isXMLStringValid($xml));
    }

    public function testInvalidXMLString(): void
    {
        $this->expectException(InvalidXmlException::class);
        $this->expectExceptionMessage(ErrorMessages::XmlNoName);
        $xml = file_get_contents(static::$incorrectXmlFile);
        $this->validator->isXMLStringValid($xml);
    }

    public function testEmptyXMLString(): void
    {
        $this->expectException(InvalidXmlException::class);
        $this->expectExceptionMessage(ErrorMessages::XmlEmptyTrimmed);
        $this->validator->isXMLStringValid('');
    }

    public function testValidXSDFile(): void
    {
        self::assertTrue($this->validator->isXMLFileValid(static::$xmlFile, static::$xsdFile));
    }

    public function testInvalidXMLFile(): void
    {
        $this->expectException(InvalidXmlException::class);
        $this->expectExceptionMessage(ErrorMessages::XmlNoName);
        $this->validator->isXMLFileValid(static::$incorrectXmlFile);
    }

    public function testInvalidXSDFile(): void
    {
        $this->expectException(InvalidXmlException::class);
        $this->validator->isXMLFileValid(static::$incorrectXmlFile, static::$xsdFile);
    }
}