<?php
/**
 * Test of Validator class
 *
 * @package go\Request
 * @subpackage Tests
 * @author Grigoriev Oleg aka vasa_c <go.vasac@gmail.com>
 */

namespace go\Tests\Request\HTTP\Helpers;

use go\Request\HTTP\Helpers\Validator;

/**
 * @covers go\Request\HTTP\Helpers\Validator
 */
class ValidatorTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @param mixed $value
     * @param string $result
     * @dataProvider providerIsFloat
     * @covers go\Request\HTTP\Helpers\Validator::isFloat
     */
    public function testIsFloat($value, $result)
    {
        $this->assertSame($result, Validator::isFloat($value));
    }

    /**
     * @return array
     */
    public function providerIsFloat()
    {
        return array(
            array('', false),
            array('scalar', false),
            array('123', true),
            array('0', true),
            array('123.456', true),
            array('-123.456', true),
            array('123e', false),
            array('0x123', false),
            array(array('1', '2'), false),
        );
    }

    /**
     * @param mixed $value
     * @param string $result
     * @dataProvider providerIsInt
     * @covers go\Request\HTTP\Helpers\Validator::isInt
     */
    public function testIsInt($value, $result)
    {
        $this->assertSame($result, Validator::isInt($value));
    }

    /**
     * @return array
     */
    public function providerIsInt()
    {
        return array(
            array('', false),
            array('scalar', false),
            array('123', true),
            array('-123', true),
            array('0', true),
            array('123.456', false),
            array('0x123', false),
            array('123e2', false),
            array(array(), false),
        );
    }

    /**
     * @param mixed $value
     * @param string $result
     * @dataProvider providerIsUInt
     * @covers go\Request\HTTP\Helpers\Validator::isUInt
     */
    public function testIsUInt($value, $result)
    {
        $this->assertSame($result, Validator::isUInt($value));
    }

    /**
     * @return array
     */
    public function providerIsUInt()
    {
        return array(
            array('', false),
            array('scalar', false),
            array('123', true),
            array('-123', false),
            array('0', true),
            array('123.456', false),
            array('0x123', false),
            array('123e2', false),
            array(array(), false),
        );
    }

    /**
     * @param mixed $value
     * @param string $result
     * @dataProvider providerIsId
     * @covers go\Request\HTTP\Helpers\Validator::isId
     */
    public function testIsId($value, $result)
    {
        $this->assertSame($result, Validator::isId($value));
    }

    /**
     * @return array
     */
    public function providerIsId()
    {
        return array(
            array('', false),
            array('scalar', false),
            array('123', true),
            array('-123', false),
            array('0', false),
            array('123.456', false),
            array('0x123', false),
            array('123e2', false),
            array(array(), false),
        );
    }

    /**
     * @param mixed $value
     * @param string $result
     * @dataProvider providerIsDictOfScalar
     * @covers go\Request\HTTP\Helpers\Validator::isDictOfScalar
     */
    public function testIsDictOfScalar($value, $result)
    {
        $this->assertSame($result, Validator::isDictOfScalar($value));
    }

    /**
     * @return array
     */
    public function providerIsDictOfScalar()
    {
        return array(
            array('', false),
            array('scalar', false),
            array('123', false),
            array(array(), true),
            array(
                array('one', 'two', 'three'),
                true,
            ),
            array(
                array('one', 'two', array('three')),
                false,
            ),
            array(
                array('one' => '1', 'two' => '2'),
                true,
            ),
            array(
                array('one' => '1', 'two' => '2', 'three' => array()),
                false,
            ),
        );
    }

    /**
     * @param mixed $value
     * @param string $result
     * @dataProvider providerIsListOfScalar
     * @covers go\Request\HTTP\Helpers\Validator::isListOfScalar
     */
    public function testIsListOfScalar($value, $result)
    {
        $this->assertSame($result, Validator::isListOfScalar($value));
    }

    /**
     * @return array
     */
    public function providerIsListOfScalar()
    {
        return array(
            array('', false),
            array('scalar', false),
            array('123', false),
            array(array(), true),
            array(
                array('one', 'two', 'three'),
                true,
            ),
            array(
                array('one', 'two', array('three')),
                false,
            ),
            array(
                array('one' => '1', 'two' => '2'),
                false,
            ),
            array(
                array('one' => '1', 'two' => '2', 'three' => array()),
                false,
            ),
        );
    }
}
