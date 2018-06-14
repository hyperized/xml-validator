<?php

namespace Hyperized\Xml\Validator\Tests;

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
    protected static $xsdFile = __DIR__ . '/files/simple.xsd';
    /**
     * @var string
     */
    protected static $xmlFile = __DIR__ . '/files/correct.xml';
    /**
     * @var string
     */
    protected static $incorrectXmlFile = __DIR__ . '/files/incorrect.xml';

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

    public function testEmptyXMLString(): void
    {
        self::assertFalse($this->validator->isXMLStringValid(''));
    }

    public function testValidXSDFile(): void
    {
        self::assertTrue($this->validator->isXMLFileValid(static::$xmlFile, static::$xsdFile));
    }

    public function testInvalidXMLFile(): void
    {
        self::assertFalse($this->validator->isXMLFileValid(static::$incorrectXmlFile));
    }

    public function testInvalidXSDFile(): void
    {
        self::assertFalse($this->validator->isXMLFileValid(static::$incorrectXmlFile, static::$xsdFile));
    }

    public function testErrors(): void
    {
        $this->validator->isXMLFileValid(static::$incorrectXmlFile);
        self::assertNotEmpty($this->validator->getErrors());
    }

    public function testPrettyErrors(): void
    {
        $this->validator->isXMLFileValid(static::$incorrectXmlFile);
        self::assertNotEmpty($this->validator->getPrettyErrors());
    }
}