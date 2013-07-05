<?php
/**
 * Test of Options class
 *
 * @package go\Request
 * @subpackage Tests
 * @author Grigoriev Oleg aka vasa_c <go.vasac@gmail.com>
 */

namespace go\Tests\Request\CLI;

use go\Request\CLI\Options;
use go\Request\CLI\Format;

/**
 * @covers go\Request\CLI\Options
 */
class OptionsTest extends \PHPUnit_Framework_TestCase
{
    public function testSuccess()
    {
        $format = array(
            'options' => array(
                'one' => array(

                ),
                'two' => array(

                ),
            ),
        );
    }

    public function testShort()
    {

    }

    public function testDefault()
    {

    }

    public function testRequired()
    {

    }

    public function testAllownUnknown()
    {

    }

    public function testFilter()
    {

    }

    public function testFilters()
    {

    }

    public function testErrors()
    {

    }
}
