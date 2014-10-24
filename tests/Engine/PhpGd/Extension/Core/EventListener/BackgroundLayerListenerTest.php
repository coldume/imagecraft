<?php

namespace Imagecraft\Engine\PhpGd\Extension\Core\EventListener;

/**
 * @covers Imagecraft\Engine\PhpGd\Extension\Core\EventListener\BackgroundLayerListener
 */
class BackgroundLayerListenerTest extends \PHPUnit_Framework_TestCase
{
    protected $listener;

    protected $event;

    protected $layer;

    public function setUp()
    {
        $this->listener = $this->getMock('Imagecraft\\Engine\\PhpGd\\Extension\\Core\\EventListener\\BackgroundLayerListener', null);
        $this->layer    = $this->getMock('Imagecraft\\Layer\\BackgroundLayer', null);
        $this->event    = $this->getMock('Imagecraft\\Engine\\PhpGd\\PhpGdEvent', [], [], '', false);
        $this->event
            ->method('getLayers')
            ->will($this->returnValue([$this->layer]))
        ;
    }

    public function testInitFinalFormat()
    {
        $this->event
            ->method('getOptions')
            ->will($this->returnValue(['output_format' => 'default']))
        ;
        $this->layer->add(['image.format' => 'png']);
        $this->listener->initFinalFormat($this->event);
        $this->assertInternalType('string', $this->layer->get('final.format'));
    }
}
