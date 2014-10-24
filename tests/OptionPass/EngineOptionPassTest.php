<?php

namespace Imagecraft\OptionPass;

/**
 * @covers Imagecraft\OptionPass\EngineOptionPass
 */
class EngineOptionPassTest extends \PHPUnit_Framework_TestCase
{
    protected $pass;

    public function setUp()
    {
        $this->pass = $this->getMock('Imagecraft\\OptionPass\\EngineOptionPass', null);
    }

    public function testProcess()
    {
        $option = $this->pass->process([]);
        $this->assertEquals('php_gd', $option['engine']);
    }
}
