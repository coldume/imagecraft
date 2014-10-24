<?php

namespace Imagecraft\OptionPass;

/**
 * @covers Imagecraft\OptionPass\DebugOptionPass
 */
class DebugOptionPassTest extends \PHPUnit_Framework_TestCase
{
    protected $pass;

    public function setUp()
    {
        $this->pass = $this->getMock('Imagecraft\\OptionPass\\DebugOptionPass', null);
    }

    public function testProcess()
    {
        $option = $this->pass->process([]);
        $this->assertTrue($option['debug']);
    }
}
