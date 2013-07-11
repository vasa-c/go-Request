<?php
/**
 * Test all system filters
 *
 * @package go\Request
 * @subpackage Tests
 * @author Grigoriev Oleg aka vasa_c <go.vasac@gmail.com>
 */

namespace go\Tests\Request\CLI\Filters;

use go\Request\CLI\Filters\Filters;

class AllTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @param mixed $filter
     * @param string $value
     * @param mixed $error [optional]
     * @dataProvider providerFilters
     */
    public function testFilters($filter, $value, $expected, $error = null)
    {
        $filter = Filters::createFilter($filter, 'opt');
        if (\is_null($error)) {
            $this->assertSame($expected, $filter->filter($value));
        } else {
            $message = ($error === true) ? '' : $error;
            $this->setExpectedException('go\Request\CLI\Filters\Error', $message);
            $filter->filter($value);
        }
    }

    /**
     * @return array
     */
    public function providerFilters()
    {
        return array(
            array(
                'Flag',
                true,
                true,
            ),
            array(
                'Flag',
                'value',
                null,
                'Option --opt is flag (cannot take value)',
            ),
            array(
                'Flag',
                '',
                null,
                'Option --opt is flag (cannot take value)',
            ),
            array(
                'Switch',
                true,
                true,
            ),
            array(
                'Switch',
                'On',
                true,
            ),
            array(
                'Switch',
                'True',
                true,
            ),
            array(
                'Switch',
                'Off',
                false,
            ),
            array(
                'Switch',
                'False',
                false,
            ),
            array(
                'Switch',
                'value',
                null,
                'Option --opt is switch (value only on/off)',
            ),
            array(
                'Value',
                'value',
                'value',
            ),
            array(
                'Value',
                true,
                null,
                'It requires value for --opt',
            ),
            array(
                'Number',
                '10',
                10,
            ),
            array(
                'Number',
                '10f',
                null,
                '--opt must be number',
            ),
            array(
                'Number',
                '-10',
                null,
                '--opt must be positive number',
            ),
            array(
                array('Number', array('signed' => true)),
                '-10',
                -10,
            ),
            array(
                array('Number'),
                '5.5',
                null,
                '--opt must be integer',
            ),
            array(
                array('Number', array('min' => 5, 'max' => 10)),
                '8',
                8,
            ),
            array(
                array('Number', array('min' => 5, 'max' => 10)),
                '2',
                null,
                '--opt must be between 5 and 10',
            ),
            array(
                array('Number', array('min' => 5, 'max' => 10)),
                '12',
                null,
                '--opt must be between 5 and 10',
            ),
            array(
                'Flag',
                null,
                false,
            ),
            array(
                'Switch',
                null,
                false,
            ),
            array(
                'Value',
                null,
                '',
            ),
            array(
                'Number',
                null,
                0,
            ),
            array(
                array('Number', array('min' => 5, 'max' => 10)),
                null,
                null,
                '--opt must be between 5 and 10',
            ),
        );
    }
}
