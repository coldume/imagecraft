<?php

namespace Imagecraft\OptionPass;

/**
 * @covers Imagecraft\OptionPass\GifAnimationOptionPass
 */
class GifAnimationOptionPassTest extends \PHPUnit_Framework_TestCase
{
    protected $pass;

    public function setUp()
    {
        $this->pass = $this->getMock('Imagecraft\\OptionPass\\GifAnimationOptionPass', null);
    }

    public function testProcess()
    {
        $option = $this->pass->process([]);
        $this->assertTrue($option['gif_animation']);
    }
}
