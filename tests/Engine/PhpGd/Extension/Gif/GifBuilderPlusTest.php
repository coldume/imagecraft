<?php

namespace Imagecraft\Engine\PhpGd\Extension\Gif;

/**
 * @covers Imagecraft\Engine\PhpGd\Extension\Gif\GifBuilderPlus
 */
class GifBuilderPlusTest extends \PHPUnit_Framework_TestCase
{
    protected $extractor;

    protected $builder;

    public function setUp()
    {
        $this->extractor = $this->getMock('Imagecraft\\Engine\\PhpGd\\Extension\\Gif\\GifExtractor', null);
        $this->builder   = $this->getMock('Imagecraft\\Engine\\PhpGd\\Extension\\Gif\\GifBuilderPlus', null);
    }

    /**
     * @dataProvider gifDataProvider
     */
    public function testGetContents($filename, $outputName)
    {
        $extracted = $this->extractor->extractFromStream($filename);

        $this->builder
            ->canvasWidth($extracted->getCanvasWidth())
            ->canvasHeight($extracted->getCanvasHeight())
            ->loop($extracted->getTotalLoops())
        ;
        for ($i = 0; $i < count($extracted); $i++) {
            $this->builder->addFrame();
            $this->builder
                ->imageWidth($extracted->getImageWidth())
                ->imageHeight($extracted->getImageHeight())
                ->imageLeft($extracted->getImageLeft())
                ->imageTop($extracted->getImageTop())
                ->dispose($extracted->getDisposalMethod())
                ->delayTime($extracted->getDelayTime())
                ->interlaceFlag($extracted->getInterlaceFlag())
                ->colorTable($extracted->getColorTable())
                ->imageData($extracted->getImageData())
            ;
            if ($extracted->getTransparentColorFlag()) {
                $this->builder->transparentColorIndex($extracted->getTransparentColorIndex());
            }
            $extracted->next();
        }
        file_put_contents(__DIR__.'/TestOutput/'.$outputName, $this->builder->getContents());
    }

    public function gifDataProvider()
    {
        return [
            [__DIR__.'/../../../../Fixtures/gif_89a_palette_alpha_animated_339x473.gif', 'gif_builder_plus_image_should_be_animated_01.gif'],
        ];
    }
}
