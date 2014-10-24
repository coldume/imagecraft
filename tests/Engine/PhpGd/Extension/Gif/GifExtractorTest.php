<?php

namespace Imagecraft\Engine\PhpGd\Extension\Gif;

/**
 * @covers Imagecraft\Engine\PhpGd\Extension\Gif\GifExtractor
 */
class GifExtractorTest extends \PHPUnit_Framework_TestCase
{
    protected $extractor;

    public function setUp()
    {
        $this->extractor = $this->getMock('Imagecraft\\Engine\\PhpGd\\Extension\\Gif\\GifExtractor', null);
    }

    /**
     * @dataProvider imageDataProvider
     */
    public function testExtractFromFilePointer($filename, $validity)
    {
        $fp = fopen($filename, 'rb');
        $extracted = $this->extractor->extractFromFilePointer($fp);
        $this->assertEquals($validity, $extracted->isValid());
    }

    /**
     * @depends      testExtractFromFilePointer
     * @dataProvider imageDataProvider
     */
    public function testExtractFromStream($filename, $validity)
    {
        $extracted = $this->extractor->extractFromStream($filename, $validity);
        $this->assertEquals($validity, $extracted->isValid());
    }

    /**
     * @depends      testExtractFromFilePointer
     * @dataProvider imageDataProvider
     */
    public function testExtractFromContents($filename, $validity)
    {
        $extracted = $this->extractor->extractFromContents(file_get_contents($filename), $validity);
        $this->assertEquals($validity, $extracted->isValid());
    }

    public function imageDataProvider()
    {
        return [
            [__DIR__.'/../../../../Fixtures/gif_89a_palette_alpha_animated_339x473.gif', true],
            [__DIR__.'/../../../../Fixtures/gif_87a_palette_250x297.gif', true],
            [__DIR__.'/../../../../Fixtures/zz_gif_89a_palette_animated_no_graphic_control_550x296.gif', true],
            [__DIR__.'/../../../../Fixtures/zz_gif_89a_palette_animated_corrupted_data_153x120.gif', false],
            [__DIR__.'/../../../../Fixtures/png_palette_alpha_3000x1174.png', false],
        ];
    }
}
