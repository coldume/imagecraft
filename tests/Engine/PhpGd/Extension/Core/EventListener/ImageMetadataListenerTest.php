<?php

namespace Imagecraft\Engine\PhpGd\Extension\Core\EventListener;

use Imagecraft\Engine\PhpGd\PhpGdContext;

/**
 * @covers Imagecraft\Engine\PhpGd\Extension\Core\EventListener\ImageMetadataListener
 */
class ImageMetadataListenerTest extends \PHPUnit_Framework_TestCase
{
    protected $listener;

    protected $event;

    protected $layer;

    protected $image;

    public function setUp()
    {
        $context = $this->getMock('Imagecraft\\Engine\\PhpGd\\PhpGdContext', null);
        $this->listener = $this->getMock(
            'Imagecraft\\Engine\PhpGd\\Extension\\Core\\EventListener\\ImageMetadataListener',
            null,
            [$context]
        );
        $this->layer = $this->getMock('Imagecraft\\Layer\\ImageLayer', null);
        $this->image = $this->getMock('Imagecraft\\Image', null);
        $this->event = $this->getMock('Imagecraft\\Engine\\PhpGd\\PhpGdEvent', [], [], '', false);
        $this->event
            ->method('getLayers')
            ->will($this->returnValue([$this->layer]))
        ;
        $this->event
            ->method('getImage')
            ->will($this->returnValue($this->image))
        ;
    }

    public function testAddImageMetadatas()
    {
        $this->layer->add([
            'final.format' => PhpGdContext::FORMAT_JPEG,
            'final.width'  => 100,
            'final.height' => 200,
            'image.width'  => 300,
            'image.height' => 400,
        ]);
        $this->listener->addImageMetadatas($this->event);
        $this->assertInternalType('string', $this->image->getMime());
        $this->assertInternalType('string', $this->image->getExtension());
        $this->assertEquals(100, $this->image->getWidth());
        $this->assertEquals(200, $this->image->getHeight());
        $this->assertEquals(300, $this->image->getExtras()['original_width']);
        $this->assertEquals(400, $this->image->getExtras()['original_height']);
    }
}
