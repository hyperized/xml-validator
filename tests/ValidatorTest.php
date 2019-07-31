<?php declare(strict_types=1);

namespace Hyperized\Xml\Tests;

use Hyperized\Xml\Constants\Strings;
use Hyperized\Xml\Validator;
use PHPUnit\Framework\TestCase;
use function is_string;

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
    private static $emptyXmlFile = __DIR__ . '/files/empty.xml';
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

    /**
     * Encoding validations
     */

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
     * String validations
     */

    public function testValidXMLString(): void
    {
        $contents = file_get_contents(static::$xmlFile);
        if (is_string($contents)) {
            self::assertTrue($this->validator->isXMLStringValid($contents));
        }
    }

    public function testInvalidXMLString(): void
    {
        $contents = file_get_contents(static::$incorrectXmlFile);
        if (is_string($contents)) {
            self::assertFalse($this->validator->isXMLStringValid($contents));
        }
    }

    public function testEmptyXMLString(): void
    {
        self::assertFalse($this->validator->isXMLStringValid(''));
    }

    /**
     * File validations- XML
     */

    public function testValidXMLFile(): void
    {
        self::assertTrue($this->validator->isXMLFileValid(static::$xmlFile));
    }

    public function testNonExistentXmlFile(): void
    {
        self::assertFalse($this->validator->isXMLFileValid(static::$nonExistentFile));
    }

    public function testFileGetContentsFalse(): void
    {
        stream_wrapper_register('invalid', InvalidStreamWrapper::class);
        self::assertFalse(@$this->validator->isXMLFileValid('invalid://foobar'));
    }

    public function testEmptyXmlFile(): void
    {
        self::assertFalse($this->validator->isXMLFileValid(static::$emptyXmlFile));
    }

    public function testInvalidXMLFile(): void
    {
        self::assertFalse($this->validator->isXMLFileValid(static::$incorrectXmlFile));
    }

    /**
     * File validations- XML with XSD
     */

    public function testValidXSDFile(): void
    {
        self::assertTrue($this->validator->isXMLFileValid(static::$xmlFile, static::$xsdFile));
    }

    public function testNonExistentXSDFile(): void
    {
        self::assertFalse($this->validator->isXMLFileValid(static::$xmlFile, static::$nonExistentFile));
    }

    public function testInvalidXSDFile(): void
    {
        self::assertFalse($this->validator->isXMLFileValid(static::$incorrectXmlFile, static::$xsdFile));
    }
}
