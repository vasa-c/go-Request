<?php
/**
 * Test of Helper\LoadForm class
 *
 * @package go\Request
 * @subpackage Tests
 * @author Grigoriev Oleg aka vasa_c <go.vasac@gmail.com>
 */

namespace go\Tests\Request\HTTP\Helpers;

use go\Request\HTTP\Helpers\LoadForm;

/**
 * @covers go\Request\HTTP\Helpers\LoadForm
 */
class LoadFormTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @covers go\Request\HTTP\Helpers\LoadForm::getDataForm
     */
    public function testGetDataForm()
    {
        $vars = array(
            'a' => '2',
            'b' => array(
                'c' => '3',
            ),
        );
        $settings = array(
            'strict' => true,
        );
        $this->assertEquals($vars, LoadForm::getDataForm(null, $settings, $vars));
        $this->assertEquals($vars['b'], LoadForm::getDataForm('b', $settings, $vars));
        $settings['name'] = 'b';
        $this->assertEquals($vars['b'], LoadForm::getDataForm(null, $settings, $vars));
        $this->assertNull(LoadForm::getDataForm('a', $settings, $vars));
        $this->assertNull(LoadForm::getDataForm('c', $settings, $vars));
    }

    /**
     * @covers go\Request\HTTP\Helpers\LoadForm::getDataForm
     */
    public function testLoadFormSimple()
    {
        $vars = array(
            'one' => 'One',
            'two' => 'Two',
            'three' => 'Three',
            'five' => 'Five',
        );
        $fields = array('one', 'two');
        $checks = array('three', 'four');
        $settings = array(
            'fields' => $fields,
            'checks' => $checks,
        );
        $expected = array(
            'one' => 'One',
            'two' => 'Two',
            'three' => true,
            'four' => false,
        );
        $this->assertEquals((object)$expected, LoadForm::load(null, $settings, $vars));
        $settings['return'] = 'array';
        $this->assertEquals($expected, LoadForm::load(null, $settings, $vars));
        $settings['return'] = 'Storage';
        $this->assertEquals('Two', LoadForm::load(null, $settings, $vars)->get('two'));
        $settings['strict'] = true;
        $this->assertNull(LoadForm::load(null, $settings, $vars));
        unset($vars['five']);
        $settings['return'] = 'array';
        $this->assertEquals($expected, LoadForm::load(null, $settings, $vars));
        $settings['fields'][] = 'six';
        $this->assertNull(LoadForm::load(null, $settings, $vars));
        $settings['throws'] = true;
        $this->setExpectedException('Exception');
        LoadForm::load(null, $settings, $vars);
    }

    /**
     * @covers go\Request\HTTP\Helpers\LoadForm::getDataForm
     */
    public function testLoadFormFormat()
    {
        $vars = array(
            'one' => ' One ',
            'two' => '2',
        );
        $settings = array(
            'format' => array(
                'one' => array(
                    'trim' => true,
                ),
                'two' => array(
                    'type' => 'int',
                    'filter' => (function ($value) {
                        if (\is_int($value)) {
                            $value *= 2;
                        }
                        return $value;
                    }),
                ),
                'three' => 'check',
            ),
            'return' => 'array',
        );
        $expected = array(
            'one' => 'One',
            'two' => 4,
            'three' => false,
        );
        $this->assertEquals($expected, LoadForm::load(null, $settings, $vars));
    }

    /**
     * @covers go\Request\HTTP\Helpers\LoadForm::getDataForm
     */
    public function testLoadFormErrorType()
    {
        $settings = array('return' => 'Storage');
        $this->setExpectedException('LogicException');
        LoadForm::load(null, $settings, array('x' => 1));
    }

    /**
     * @covers go\Request\HTTP\Helpers\LoadForm::getDataForm
     */
    public function testLoadFormName()
    {
        $vars = array(
            'x' => '1',
            'y' => '2',
            'z' => array(
                'x' => '3',
                'y' => '4',
            ),
        );
        $settings = array(
            'fields' => array('x', 'y'),
        );
        $this->assertEquals('1', LoadForm::load(null, $settings, $vars)->x);
        $this->assertEquals('3', LoadForm::load('z', $settings, $vars)->x);
        $settings['name'] = 'z';
        $this->assertEquals('3', LoadForm::load(null, $settings, $vars)->x);
        $this->assertNull(LoadForm::load('qwe', $settings, $vars));
    }

    /**
     * @covers go\Request\HTTP\Helpers\LoadForm::getDataForm
     */
    public function testRecursive()
    {
        $vars = array(
            'x' => '1',
            'y' => array(
                'a' => '2',
                'b' => ' B ',
                'c' => array(
                    'd' => '4',
                ),
            ),
        );
        $settings = array(
            'format' => array(
                'a' => 'int',
                'b' => array(
                    'trim' => true,
                ),
                'c' => array(
                    'filter' => (function ($value) {
                        $value['e'] = '5';
                        return $value;
                    }),
                    'format' => array(
                        'd' => 'int',
                        'e' => 'int',
                    ),
                ),
            ),
            'return' => 'array',
        );
        $expected = array(
            'a' => 2,
            'b' => 'B',
            'c' => array(
                'd' => 4,
                'e' => 5,
            ),
        );
        $this->assertEquals($expected, LoadForm::load('y', $settings, $vars));
        unset($settings['return']);
        $this->assertEquals(5, LoadForm::load('y', $settings, $vars)->c->e);
    }
}
