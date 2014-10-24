<?php

namespace Imagecraft\Engine\PhpGd\Extension\Core\EventListener;

use Imagecraft\Engine\PhpGd\PhpGdContext;

/**
 * @covers Imagecraft\Engine\PhpGd\Extension\Core\EventListener\MemoryRequirementListener
 */
class MemoryRequirementListenerTest extends \PHPUnit_Framework_TestCase
{
    protected $listener;

    protected $event;

    protected $layer;

    protected $image;

    public function setUp()
    {
        $context        = $this->getMock('Imagecraft\\Engine\\PhpGd\\PhpGdContext', null);
        $this->listener = $this->getMock(
            'Imagecraft\\Engine\PhpGd\\Extension\\Core\\EventListener\\MemoryRequirementListener',
            null,
            [$context]
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

    /**
     * @expectedException Imagecraft\Exception\RuntimeException
     */
    public function testVerifyMemoryLimit()
    {
        $this->event
            ->method('getOptions')
            ->will($this->returnValue(30 * 1024 * 1024))
        ;
        $this->layer->add([
            'image.format' => PhpGdContext::FORMAT_JPEG,
            'image.width'  => 1000000,
            'image.height' => 1000000,
            'final.width'  => 200,
            'final.height' => 200,
        ]);
        $this->listener->verifyMemoryLimit($this->event);
    }

    public function testAddImageExtras()
    {
        $this->event
            ->method('getOptions')
            ->will($this->returnValue(50 * 1024 * 1024))
        ;
        $this->layer->add([
            'image.format' => PhpGdContext::FORMAT_JPEG,
            'image.width'  => 100,
            'image.height' => 100,
            'final.width'  => 100,
            'final.height' => 100,
        ]);
        $this->listener->verifyMemoryLimit($this->event);
        $this->listener->addImageExtras($this->event);
        $this->assertNotEmpty($this->image->getExtras()['memory_approx']);
    }
}
