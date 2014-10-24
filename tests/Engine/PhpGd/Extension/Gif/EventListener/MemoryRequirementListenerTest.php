<?php

namespace Imagecraft\Engine\PhpGd\Extension\Gif\EventListener;

/**
 * @covers Imagecraft\Engine\PhpGd\Extension\Gif\EventListener\MemoryRequirementListener
 */
class MemoryRequirementListenerTest extends \PHPUnit_Framework_TestCase
{
    protected $context;

    protected $listener;

    protected $event;

    protected $layer;

    protected $image;

    public function setUp()
    {
        $this->context  = $this->getMock('Imagecraft\\Engine\\PhpGd\\PhpGdContext', null);
        $this->listener = $this->getMock(
            'Imagecraft\\Engine\\PhpGd\\Extension\\Gif\\EventListener\\MemoryRequirementListener',
            null,
            [$this->context]
        );
        $this->layer = $this->getMock('Imagecraft\\Layer\\BackgroundLayer', null);
        $this->event = $this->getMock('Imagecraft\\Engine\\PhpGd\\PhpGdEvent', [], [], '', false);
        $this->event
            ->method('getLayers')
            ->will($this->returnValue([$this->layer]))
        ;
        $this->image = $this->getMock('Imagecraft\\Image', null);
        $this->event
            ->method('getImage')
            ->will($this->returnValue($this->image))
        ;
    }

    public function testVerifyMemoryLimitWhenImageIsCompatible()
    {
        $this->event
            ->expects($this->once())
            ->method('getOptions')
            ->will($this->returnValue(['memory_limit' => '120']))
        ;
        $this->layer->add([
            'gif.extracted' => [0, 0, 0, 0],
            'image.width'   => 100,
            'image.height'  => 100,
            'final.width'   => 10,
            'final.height'  => 10,
        ]);
        $this->listener->verifyMemoryLimit($this->event);
        $this->listener->addImageExtras($this->event);
        $this->assertArrayHasKey('memory_approx', $this->image->getExtras());
    }

    public function testVerifyMemoryLimitWhenImageIsIncompatible()
    {
        $this->event
            ->expects($this->once())
            ->method('getOptions')
            ->will($this->returnValue(['memory_limit' => '120']))
        ;
        $this->layer->add([
            'gif.extracted' => [0, 0, 0, 0],
            'image.width'   => 10000000000000000000000,
            'image.height'  => 10000000000000000000000,
            'final.width'   => 10,
            'final.height'  => 10,
        ]);
        $this->listener->verifyMemoryLimit($this->event);
        $this->listener->addImageExtras($this->event);
        $this->assertArrayHasKey('gif_error', $this->image->getExtras());
    }
}
