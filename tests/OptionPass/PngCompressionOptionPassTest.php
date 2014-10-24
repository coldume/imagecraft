<?php

namespace Imagecraft\OptionPass;

/**
 * @covers Imagecraft\OptionPass\PngCompressionOptionPass
 */
class PngCompressionOptionPassTest extends \PHPUnit_Framework_TestCase
{
    protected $pass;

    public function setUp()
    {
        $this->pass = $this->getMock('Imagecraft\\OptionPass\\PngCompressionOptionPass', null);
    }

    public function testProcess()
    {
        $option = $this->pass->process([]);
        $this->assertEquals(100, $option['png_compression']);

        $option = $this->pass->process(['png_compression' => 200]);
        $this->assertEquals(100, $option['png_compression']);

        $option = $this->pass->process(['png_compression' => -100]);
        $this->assertEquals(100, $option['png_compression']);
    }
}
