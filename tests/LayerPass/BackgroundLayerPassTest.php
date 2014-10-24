<?php

namespace Imagecraft\LayerPass;

/**
 * @covers Imagecraft\LayerPass\BackgroundLayerPass
 */
class BackgroundLayerPassTest extends \PHPUnit_Framework_TestCase
{
    protected $pass;

    protected $layer;

    public function setUp()
    {
        $this->pass  = $this->getMock('Imagecraft\\LayerPass\\BackgroundLayerPass', null);
        $this->layer = $this->getMock('Imagecraft\\Layer\\BackgroundLayer', null);
    }

    /**
     * @expectedException Imagecraft\Exception\BadMethodCallException
     */
    public function testProcessWhenNoBackgroundLayerIsFound()
    {
        $this->pass->process([]);
    }
}
