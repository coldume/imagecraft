<?php

namespace Imagecraft\OptionPass;

/**
 * @covers Imagecraft\OptionPass\MemoryLimitOptionPass
 */
class MemoryLimitOptionPassTest extends \PHPUnit_Framework_TestCase
{
    protected $pass;

    public function setUp()
    {
        $this->pass = $this->getMock('Imagecraft\\OptionPass\\MemoryLimitOptionPass', null);
    }

    public function testProcess()
    {
        $option = $this->pass->process([]);
        $this->assertEquals(-10, $option['memory_limit']);
    }
}
