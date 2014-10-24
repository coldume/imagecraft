<?php

namespace Imagecraft\Engine\PhpGd\Extension\Core\EventListener;

/**
 * @covers Imagecraft\Engine\PhpGd\Extension\Core\EventListener\ImageFactoryListener
 */
class ImageFactoryListenerTest extends \PHPUnit_Framework_TestCase
{
    protected $listener;

    protected $event;

    protected $factory;

    public function setUp()
    {
        $this->factory  = $this->getMock('Imagecraft\\Engine\\PhpGd\\Extension\\Core\\ImageFactory', [], [], '', false);
        $this->listener = $this->getMock(
            'Imagecraft\\Engine\PhpGd\\Extension\\Core\\EventListener\\ImageFactoryListener',
            null,
            [$this->factory]
        );
        $this->event = $this->getMock('Imagecraft\\Engine\\PhpGd\\PhpGdEvent', [], [], '', false);
    }

    public function testCreateImage()
    {
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
        $this->event
            ->expects($this->once())
            ->method('getLayers')
            ->will($this->returnValue([]))
        ;
        $this->event
            ->expects($this->once())
            ->method('getOptions')
            ->will($this->returnValue([]))
        ;
        $this->listener->createImage($this->event);
    }
}
