<?php

namespace Imagecraft\OptionPass;

/**
 * @covers Imagecraft\OptionPass\CacheDirOptionPass
 */
class CacheDirOptionPassTest extends \PHPUnit_Framework_TestCase
{
    protected $pass;

    public function setUp()
    {
        $this->pass = $this->getMock('Imagecraft\\OptionPass\\CacheDirOptionPass', null);
    }

    public function testProcess()
    {
        $option = $this->pass->process([]);
        $this->assertEquals(null, $option['cache_dir']);
    }
}
