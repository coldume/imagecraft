<?php

namespace Imagecraft\OptionPass;

/**
 * @covers Imagecraft\OptionPass\OutputFormatOptionPass
 */
class OutputFormatOptionPassTest extends \PHPUnit_Framework_TestCase
{
    protected $pass;

    public function setUp()
    {
        $this->pass = $this->getMock('Imagecraft\\OptionPass\\OutputFormatOptionPass', null);
    }

    public function testProcess()
    {
        $option = $this->pass->process([]);
        $this->assertEquals('default', $option['output_format']);
    }
}
