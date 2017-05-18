<?php

namespace Hyperized\Xml\Validator\Tests;

use Hyperized\Xml\Validator;
use PHPUnit\Framework\TestCase;

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

    public function testValidXMLFile()
    {
        $validator = new Validator();
        self::assertTrue($validator->isXMLFileValid(static::$xmlFile));
    }

    public function testValidXMLString()
    {
        $xml = file_get_contents(static::$xmlFile);
        $validator = new Validator();
        self::assertTrue($validator->isXMLStringValid($xml));
    }

    public function testEmptyXMLString()
    {
        $this->expectException(\Exception::class);
        $validator = new Validator();
        self::assertTrue($validator->isXMLStringValid(''));
    }

    public function testValidXSDFile()
    {
        $validator = new Validator();
        self::assertTrue($validator->isXMLFileValid(static::$xmlFile, static::$xsdFile));
    }

    public function testInvalidXMLFile()
    {
        $this->expectException(\Exception::class);
        $validator = new Validator();
        $validator->isXMLFileValid(static::$incorrectXmlFile);
    }

    public function testInvalidXSDFile()
    {
        $this->expectException(\Exception::class);
        $validator = new Validator();
        $validator->isXMLFileValid(static::$incorrectXmlFile, static::$xsdFile);
    }

    public function testErrors()
    {
        $validator = new Validator();
        try {
            $validator->isXMLFileValid(static::$incorrectXmlFile);
        } catch (\Exception $exception) {
            $errors = $validator->getErrors();
        }
        self::assertNotEmpty($errors);
    }

    public function testPrettyErrors()
    {
        $validator = new Validator();
        try {
            $validator->isXMLFileValid(static::$incorrectXmlFile);
        } catch (\Exception $exception) {
            $errors = $validator->getPrettyErrors();
        }
        self::assertNotEmpty($errors);
    }
}