<?php

namespace Imagecraft\Engine\PhpGd\Extension\Gif\EventListener;

use Imagecraft\Engine\PhpGd\PhpGdContext;

/**
 * @covers Imagecraft\Engine\PhpGd\Extension\Gif\EventListener\GifExtractorListener
 */
class GifExtractorListenerTest extends \PHPUnit_Framework_TestCase
{
    protected $listener;

    protected $event;

    protected $layer;

    protected $image;

    public function setUp()
    {
        $extractor      = $this->getMock('Imagecraft\\Engine\\PhpGd\\Extension\\Gif\\GifExtractor', null);
        $this->listener = $this->getMock(
            'Imagecraft\\Engine\\PhpGd\\Extension\\Gif\\EventListener\\GifExtractorListener',
            null,
            [$extractor]
        );
        $this->layer = $this->getMock('Imagecraft\\Layer\\BackgroundLayer', null);
        $this->layer->add(['final.format' => PhpGdContext::FORMAT_GIF, 'image.format' => PhpGdContext::FORMAT_GIF]);
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
     * @dataProvider validAnimatedGifDataProvider
     */
    public function testInitExtracted($filename)
    {
        $this->event
            ->method('getOptions')
            ->will($this->returnValue(['gif_animation' => true]))
        ;
        $this->layer->set('image.fp', fopen($filename, 'rb'));
        $this->listener->initExtracted($this->event);
        $this->assertTrue($this->layer->has('gif.extracted'));
    }

    /**
     * @dataProvider invalidAnimatedGifDataProvider
     */
    public function testInitExtractedWhenGifIsInvalid($filename)
    {
        $this->event
            ->method('getOptions')
            ->will($this->returnValue(['gif_animation' => true]))
        ;
        $this->layer->set('image.fp', fopen($filename, 'rb'));
        $this->listener->initExtracted($this->event);
        $this->assertFalse($this->layer->has('gif.extracted'));
    }

    /**
     * @depends      testInitExtractedWhenGifIsInvalid
     * @dataProvider corruptedAnimatedGifDataProvider
     */
    public function testAddImageExtras($filename)
    {
        $this->event
            ->method('getOptions')
            ->will($this->returnValue(['gif_animation' => true]))
        ;
        $this->layer->set('image.fp', fopen($filename, 'rb'));
        $this->listener->initExtracted($this->event);
        $this->listener->addImageExtras($this->event);
        $this->assertNotEmpty($this->image->getExtras()['gif_error']);
    }

    public function validAnimatedGifDataProvider()
    {
        return [
            [__DIR__.'/../../../../../Fixtures/gif_89a_palette_alpha_animated_339x473.gif'],
        ];
    }

    public function invalidAnimatedGifDataProvider()
    {
        return [
            [__DIR__.'/../../../../../Fixtures/gif_87a_palette_250x297.gif'],
            [__DIR__.'/../../../../../Fixtures/gif_89a_palette_alpha_206x205.gif'],
            [__DIR__.'/../../../../../Fixtures/zz_gif_89a_palette_animated_corrupted_data_153x120.gif'],
            [__DIR__.'/../../../../../Fixtures/jpeg_jfjf_truecolor_1920x758.jpg'],
        ];
    }

    public function corruptedAnimatedGifDataProvider()
    {
        return [
            [__DIR__.'/../../../../../Fixtures/zz_gif_89a_palette_animated_corrupted_data_153x120.gif'],
        ];
    }
}
