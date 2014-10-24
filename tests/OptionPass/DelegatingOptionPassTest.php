<?php

namespace Imagecraft\OptionPass;

/**
 * @covers Imagecraft\OptionPass\DelegatingOptionPass
 */
class DelegatingOptionPassTest extends \PHPUnit_Framework_TestCase
{
    protected $pass;

    public function setUp()
    {
        $this->pass = $this->getMock('Imagecraft\\OptionPass\\DelegatingOptionPass', null);
    }

    public function testProcess()
    {
        $option = $this->pass->process([]);
        $this->assertInternalType('array', $option);
    }
}
