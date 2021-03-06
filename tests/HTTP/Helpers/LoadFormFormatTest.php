<?php
/**
 * Test of Helper\LoadFormFormat class
 *
 * @package go\Request
 * @subpackage Tests
 * @author Grigoriev Oleg aka vasa_c <go.vasac@gmail.com>
 */

namespace go\Tests\Request\HTTP\Helpers;

use go\Request\HTTP\Helpers\LoadFormFormat;

/**
 * @covers go\Request\HTTP\Helpers\LoadFormFormat
 */
class LoadFormFormatTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @covers go\Request\HTTP\Helpers\LoadFormFormat::normalizeParams
     * @dataProvider providerNormalizeParams
     * @param mixed $params
     * @param array $expected
     */
    public function testNormalizeParams($params, $expected)
    {
        $this->assertEquals($expected, LoadFormFormat::normalizeParams($params));
    }

    /**
     * @return array
     */
    public function providerNormalizeParams()
    {
        return array(
            array(
                true,
                array(
                    'type' => 'scalar',
                ),
            ),
            array(
                'int',
                array(
                    'type' => 'int',
                ),
            ),
            array(
                array(
                    'trim' => true,
                ),
                array(
                    'type' => 'scalar',
                    'trim' => true,
                ),
            ),
            array(
                array(
                    'format' => array(),
                ),
                array(
                    'type' => 'array',
                    'format' => array(),
                ),
            ),
            array(
                array(
                    'type' => 'int',
                    'format' => array(),
                ),
                array(
                    'type' => 'int',
                    'format' => array(),
                ),
            ),
        );
    }

    /**
     * @covers go\Request\HTTP\Helpers\LoadFormFormat::filter
     * @dataProvider providerFilter
     * @param mixed $value
     * @param array $params
     * @param mixed $expected
     */
    public function testFilter($value, $params, $expected)
    {
        $this->assertSame($expected, LoadFormFormat::filter($value, $params));
    }

    /**
     * @return array
     */
    public function providerFilter()
    {
        $filter = (function ($value) {
            return \strtolower($value);
        });
        return array(
            array(
                '  This is test String ',
                array(),
                '  This is test String ',
            ),
            array(
                '  This is test String ',
                array(
                    'trim' => true,
                ),
                'This is test String',
            ),
            array(
                '  This is test String ',
                array(
                    'trim' => 'left',
                ),
                'This is test String ',
            ),
            array(
                '  This is test String ',
                array(
                    'trim' => 'right',
                ),
                '  This is test String',
            ),
            array(
                '  This is test String ',
                array(
                    'filter' => $filter,
                ),
                '  this is test string ',
            ),
            array(
                '  This is test String ',
                array(
                    'trim' => true,
                    'filter' => $filter,
                ),
                'this is test string',
            ),
        );
    }

    /**
     * @covers go\Request\HTTP\Helpers\LoadFormFormat::validate
     * @dataProvider providerValidate
     * @param mixed $value
     * @param array $params
     * @param boolean $expected
     */
    public function testValidate($value, $params, $expected)
    {
        $this->assertSame($expected, LoadFormFormat::validate($value, $params));
    }

    /**
     * @return array
     */
    public function providerValidate()
    {
        $validator = (function ($value) {
            return ($value === 'value');
        });
        return array(
            array(
                '',
                array(
                ),
                true,
            ),
            array(
                'dsfdgt',
                array(
                    'notempty' => true,
                ),
                true,
            ),
            array(
                '',
                array(
                    'notempty' => true,
                ),
                false,
            ),
            array(
                'Two',
                array(
                    'notempty' => true,
                    'enum' => array('One', 'Two', 'Three'),
                ),
                true,
            ),
            array(
                'Four',
                array(
                    'enum' => array('One', 'Two', 'Three'),
                ),
                false,
            ),
            array(
                '123',
                array(
                    'match' => '/^[1-3]+$/s',
                ),
                true,
            ),
            array(
                '1243',
                array(
                    'match' => '/^[1-3]+$/s',
                ),
                false,
            ),
            array(
                'Рус',
                array(
                    'maxlength' => 3,
                ),
                true,
            ),
            array(
                'Русский',
                array(
                    'maxlength' => 3,
                ),
                false,
            ),
            array(
                12,
                array(
                    'range' => array(3, 12),
                ),
                true,
            ),
            array(
                15,
                array(
                    'range' => array(3, 12),
                ),
                false,
            ),
            array(
                15,
                array(
                    'range' => array(3, null),
                ),
                true,
            ),
            array(
                'value',
                array(
                    'validator' => $validator,
                ),
                true,
            ),
            array(
                'novalue',
                array(
                    'validator' => $validator,
                ),
                false,
            ),
            array(
                'value',
                array(
                    'maxlength' => 2,
                    'validator' => $validator,
                ),
                false,
            ),
        );
    }

    /**
     * @covers go\Request\HTTP\Helpers\LoadFormFormat::loadField
     * @dataProvider providerLoadField
     * @param array $vars
     * @param string $name
     * @param array $params
     * @param mixed $expected
     * @param boolean $ok
     */
    public function testLoadField($vars, $name, $params, $expected, $ok)
    {
        $actual = LoadFormFormat::loadField($vars, $name, $params, $nok);
        if (!$ok) {
            $this->assertFalse($nok);
        } else {
            $this->assertTrue($nok);
            $this->assertSame($expected, $actual);
        }
    }

    /**
     * @return array
     */
    public function providerLoadField()
    {
        $vars = array(
            'one' => 'One',
            'two' => '2',
            'str' => '  Str  ',
        );
        return array(
            array($vars, 'unknown', array(), null, false),
            array($vars, 'one', array(), 'One', true),
            array($vars, 'one', 'uint', null, false),
            array($vars, 'two', 'uint', 2, true),
            array($vars, 'two', 'check', true, true),
            array($vars, 'unknown', 'check', false, true),
            array($vars, 'str', array('trim' => true), 'Str', true),
            array($vars, 'str', array('maxlength' => 3), null, false),
            array($vars, 'str', array('trim' => true, 'maxlength' => 3), 'Str', true),
        );
    }

    public function testLoad()
    {
        $format = array(
            'name' => array(
                'trim' => true,
            ),
            'age' => array(
                'uint' => true,
                'range' => array(18, null),
            ),
            'sex' => array(
                'enum' => array('male', 'female'),
            ),
            'ok' => 'check',
        );
        $vars1 = array(
            'name' => '  Vasa ',
            'age' => '25',
            'sex' => 'male',
            'other' => 'Other',
        );
        $expected1 = array(
            'name' => 'Vasa',
            'age' => 25,
            'sex' => 'male',
            'ok' => false,
        );
        $this->assertEquals($expected1, LoadFormFormat::load($vars1, $format));
        $this->assertEquals((object)$expected1, LoadFormFormat::load($vars1, $format, true));
        $this->assertNull(LoadFormFormat::load($vars1, $format, false, true));
        unset($vars1['other']);
        $this->assertEquals($expected1, LoadFormFormat::load($vars1, $format, false, true));

        $vars2 = array(
            'name' => '  Vasa ',
            'age' => '12',
            'sex' => 'male',
        );
        $this->assertNull(LoadFormFormat::load($vars2, $format));
    }

    public function testReqursive()
    {
        $format = array(
            'name' => true,
            'email' => true,
            'ok' => 'check',
            'address' => array(
                'format' => array(
                    'country' => array(
                        'trim' => true,
                    ),
                    'city' => true,
                    'tramp' => 'check',
                ),
            ),
        );
        $vars = array(
            'name' => ' Name',
            'email' => 'e@mail',
            'ok' => '1',
            'address' => array(
                'country' => ' Russia ',
                'city' => 'Spb',
                'wow' => 'wow',
            ),
        );
        $expected = array(
            'name' => ' Name',
            'email' => 'e@mail',
            'ok' => true,
            'address' => array(
                'country' => 'Russia',
                'city' => 'Spb',
                'tramp' => false,
            ),
        );
        $this->assertEquals($expected, LoadFormFormat::load($vars, $format));
        $this->assertEquals('Spb', LoadFormFormat::load($vars, $format, true)->address->city);
        $this->assertNull(LoadFormFormat::load($vars, $format, false, true));
        unset($vars['address']['wow']);
        $this->assertEquals($expected, LoadFormFormat::load($vars, $format, false, true));
    }
}
