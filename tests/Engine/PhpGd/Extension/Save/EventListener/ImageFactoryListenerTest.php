<?php

namespace Imagecraft\Engine\PhpGd\Extension\Save\EventListener;

/**
 * @covers Imagecraft\Engine\PhpGd\Extension\Save\EventListener\ImageFactoryListener
 */
class ImageFactoryListenerTest extends \PHPUnit_Framework_TestCase
{
    protected $listener;

    protected $event;

    protected $factory;

    protected $layer;

    public function setUp()
    {
        $this->factory  = $this->getMock('Imagecraft\\Engine\\PhpGd\\Extension\\Save\\ImageFactory', [], [], '', false);
        $this->listener = $this->getMock(
            'Imagecraft\\Engine\PhpGd\\Extension\\Save\\EventListener\\ImageFactoryListener',
            null,
            [$this->factory]
        );
        $this->layer = $this->getMock('Imagecraft\\Layer\\ImageLayer', null);
        $this->event = $this->getMock('Imagecraft\\Engine\\PhpGd\\PhpGdEvent', [], [], '', false);
        $this->event
            ->method('getLayers')
            ->will($this->returnValue([$this->layer]))
        ;
        $this->event
            ->expects($this->once())
            ->method('getOptions')
            ->will($this->returnValue([]))
        ;
    }

    public function testCreateImage()
    {
        $this->layer->add([
            'image.width'  => 10,
            'image.height' => 100,
            'final.width'  => 10,
            'final.height' => 100,
            'image.format' => 'foo',
            'final.format' => 'foo',
        ]);
        $image = $this->getMock('Imagecraft\\Image', null);
        $this->factory
            ->expects($this->once())
            ->method('createImage')
            ->will($this->returnValue($image))
        ;
        $this->event
            ->expects($this->once())
            ->method('setImage')
            ->with($image)
        ;
        $this->listener->createImage($this->event);
    }
}
