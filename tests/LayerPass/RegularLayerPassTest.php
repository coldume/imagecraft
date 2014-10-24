<?php

namespace Imagecraft\LayerPass;

use Imagecraft\Layer\RegularLayerInterface;

/**
 * @covers Imagecraft\LayerPass\RegularLayerPass
 */
class RegularLayerPassTest extends \PHPUnit_Framework_TestCase
{
    protected $pass;

    protected $layer;

    public function setUp()
    {
        $this->pass  = $this->getMock('Imagecraft\\LayerPass\\RegularLayerPass', null);
        $this->layer = $this->getMock('Imagecraft\\Layer\\ImageLayer', null);
    }

    public function testProcessMove()
    {
        $this->pass->processMove($this->layer);
        $this->assertTrue($this->layer->has('regular.move.x'));
        $this->assertTrue($this->layer->has('regular.move.y'));
        $this->assertTrue($this->layer->has('regular.move.gravity'));

        $this->layer->clear();
        $this->layer->add([
            'regular.move.x'       => 11.1,
            'regular.move.y'       => -10,
            'regular.move.gravity' => RegularLayerInterface::MOVE_TOP_LEFT,

        ]);
        $this->pass->processMove($this->layer);
        $this->assertEquals(11, $this->layer->get('regular.move.x'));
        $this->assertEquals(-10, $this->layer->get('regular.move.y'));
        $this->assertEquals(RegularLayerInterface::MOVE_TOP_LEFT, $this->layer->get('regular.move.gravity'));
    }
}
