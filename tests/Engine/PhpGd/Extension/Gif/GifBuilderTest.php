<?php

namespace Imagecraft\Engine\PhpGd\Extension\Gif;

/**
 * @covers Imagecraft\Engine\PhpGd\Extension\Gif\GifBuilder
 */
class GifBuilderTest extends \PHPUnit_Framework_TestCase
{
    protected $extractor;

    public function setUp()
    {
        $this->extractor = $this->getMock('Imagecraft\\Engine\\PhpGd\\Extension\\Gif\\GifExtractor', null);
        $this->builder   = $this->getMock('Imagecraft\\Engine\\PhpGd\\Extension\\Gif\\GifBuilder', null);
    }

    /**
     * @dataProvider gifDataProvider
     */
    public function testGetContents($filename, $outputName)
    {
        @mkdir(__DIR__.'/TestOutput/'.$outputName);
        $extracted = $this->extractor->extractFromStream($filename);
        foreach ($extracted as $key => $frame) {
            $this->builder
                ->imageWidth($extracted->getImageWidth())
                ->imageHeight($extracted->getImageHeight())
                ->colorTable($extracted->getColorTable())
                ->interlaceFlag($extracted->getInterlaceFlag())
                ->imageData($extracted->getImageData())
            ;
            if ($extracted->getTransparentColorFlag()) {
                $index = $extracted->getTransparentColorIndex();
                $this->builder->transparentColorIndex($index);
            }
            $contents = $this->builder->getContents();
            file_put_contents(__DIR__.'/TestOutput/'.$outputName.'/'.$key.'.gif', $contents);
        }
    }

    public function gifDataProvider()
    {
        return [
            [__DIR__.'/../../../../Fixtures/gif_89a_palette_alpha_animated_339x473.gif', 'gif_builder_frames_should_be_valid_01'],
        ];
    }
}
