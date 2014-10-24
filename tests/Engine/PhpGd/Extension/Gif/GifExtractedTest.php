<?php

namespace Imagecraft\Engine\PhpGd\Extension\Gif;

/**
 * @covers Imagecraft\Engine\PhpGd\Extension\Gif\GifExtracted
 */
class GifExtractedTest extends \PHPUnit_Framework_TestCase
{
    protected $extractor;

    public function setUp()
    {
        $this->extractor = $this->getMock('Imagecraft\\Engine\\PhpGd\\Extension\\Gif\\GifExtractor', null);
    }

    /**
     * @dataProvider gifDataProvider
     */
    public function testGettersForAnimatedGif($filename, $specs)
    {
        $fp = fopen($filename, 'rb');
        $extracted = $this->extractor->extractFromFilePointer($fp);
        $this->assertTrue($extracted->isValid());
        $this->assertTrue($extracted->isAnimated());

        foreach ($extracted as $key => $frame) {
            if (0 === $key) {
                continue;
            }
            $this->assertNotEmpty($extracted->getImageData());
            $this->assertNotEmpty($extracted->getColorTable());
        }

        $extracted->seek(2);
        $this->assertEquals($specs['total_frames'], count($extracted));
        $this->assertEquals($specs['canvas_width'], $extracted->getCanvasWidth());
        $this->assertEquals($specs['canvas_height'], $extracted->getCanvasHeight());
        $this->assertEquals($specs['global_flag'], $extracted->getGlobalColorTableFlag());
        if ($specs['global_flag']) {
            $this->assertEquals($specs['total_globals'], $extracted->getTotalGlobalColors());
        }
        $this->assertEquals($specs['total_loops'], $extracted->getTotalLoops());
        $this->assertEquals($specs['disposal_method'], $extracted->getDisposalMethod());
        $this->assertEquals($specs['transparent_flag'], $extracted->getTransparentColorFlag());
        if ($specs['transparent_flag']) {
            $this->assertEquals($specs['transparent_index'], $extracted->getTransparentColorIndex());
        }
        $this->assertEquals($specs['delay_time'], $extracted->getDelayTime());
        $this->assertEquals($specs['image_left'], $extracted->getImageLeft());
        $this->assertEquals($specs['image_top'], $extracted->getImageTop());
        $this->assertEquals($specs['image_width'], $extracted->getImageWidth());
        $this->assertEquals($specs['image_height'], $extracted->getImageHeight());
        $this->assertEquals($specs['local_flag'], $extracted->getLocalColorTableFlag());
        if ($specs['local_flag']) {
            $this->assertEquals($specs['total_locals'], $extracted->getTotalLocalColors());
        }
        $this->assertEquals($specs['interlace_flag'], $extracted->getInterlaceFlag());
        $this->assertNotEmpty($extracted->getLinkedKey());
        $this->assertNotEmpty($extracted->getLinkedDisposalMethod());
    }

    public function gifDataProvider()
    {
        $filename1 = __DIR__.'/../../../../Fixtures/gif_89a_palette_alpha_animated_339x473.gif';
        $specs1    = [
            'total_frames'      => 19,
            'canvas_width'      => 339,
            'canvas_height'     => 473,
            'global_flag'       => true,
            'total_globals'     => 256,
            'total_loops'       => 0,
            'disposal_method'   => 2,
            'transparent_flag'  => true,
            'transparent_index' => 53,
            'delay_time'        => 10,
            'image_left'        => 0,
            'image_top'         => 0,
            'image_width'       => 339,
            'image_height'      => 473,
            'local_flag'        => false,
            'interlace_flag'    => false,
        ];
        return [
            [$filename1, $specs1],
        ];
    }
}
