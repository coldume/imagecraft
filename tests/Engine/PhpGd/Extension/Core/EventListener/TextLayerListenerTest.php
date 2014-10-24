<?php

namespace Imagecraft\Engine\PhpGd\Extension\Core\EventListener;

/**
 * @covers Imagecraft\Engine\PhpGd\Extension\Core\EventListener\TextLayerListener
 */
class TextLayerListenerTest extends \PHPUnit_Framework_TestCase
{
    protected $context;

    protected $listener;

    protected $event;

    protected $layer;

    public function setUp()
    {
        $this->context  = $this->getMock('Imagecraft\\Engine\\PhpGd\\PhpGdContext');
        $this->listener = $this->getMock(
            'Imagecraft\\Engine\PhpGd\\Extension\\Core\\EventListener\\TextLayerListener',
            null,
            [$this->context]
        );
        $this->layer = $this->getMock('Imagecraft\\Layer\\TextLayer', null);
        $this->event = $this->getMock('Imagecraft\\Engine\\PhpGd\\PhpGdEvent', [], [], '', false);
        $this->event
            ->method('getLayers')
            ->will($this->returnValue([$this->layer]))
        ;
    }

    /**
     * @expectedException Imagecraft\Exception\RuntimeException
     */
    public function testVerifyFreeType()
    {
        $this->context
            ->expects($this->once())
            ->method('isFreeTypeSupported')
            ->will($this->returnValue(false))
        ;
        $this->listener->verifyFreeType($this->event);
    }
}
