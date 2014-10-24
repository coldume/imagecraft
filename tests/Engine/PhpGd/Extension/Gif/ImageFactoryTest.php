<?php

namespace Imagecraft\Engine\PhpGd\Extension\Gif;

use Imagecraft\Layer\BackgroundLayer;
use Imagecraft\Layer\ImageLayer;
use Imagecraft\Layer\TextLayer;
use Imagecraft\Layer\ImageAwareLayerInterface;
use Imagecraft\Layer\RegularLayerInterface;
use Imagecraft\Engine\PhpGd\PhpGdContext;

/**
 * @requires extension gd
 * @requires function imagegif
 * @requires function imagecreatefromgif
 * @requires function imagecreatefromwebp
 * @requires function imagecreatefromjpeg
 * @requires function imagecreatefrompng
 * @requires function imagefttext
 * @covers   Imagecraft\Engine\PhpGd\Extension\Gif\ImageFactory
 */
class ImageFactoryTest extends \PHPUnit_Framework_TestCase
{
    protected $factory;

    public function setUp()
    {
        $rh            = $this->getMock('Imagecraft\\Engine\\PhpGd\\Helper\\ResourceHelper', null);
        $extractor     = $this->getMock('Imagecraft\\Engine\\PhpGd\\Extension\\Gif\\GifExtractor', null);
        $builder       = $this->getMock('Imagecraft\\Engine\\PhpGd\\Extension\\Gif\\GifBuilder', null);
        $builderPlus   = $this->getMock('Imagecraft\\Engine\\PhpGd\\Extension\\Gif\\GifBuilderPlus', null);
        $optimizer     = $this->getMock('Imagecraft\\Engine\\PhpGd\\Extension\\Gif\\GifOptimizer', null, [$rh]);
        $this->factory = $this->getMock(
            'Imagecraft\\Engine\\PhpGd\\Extension\\Gif\\ImageFactory',
            null,
            [$rh, $extractor, $builder, $builderPlus, $optimizer]
        );
    }

    /**
     * @dataProvider layerDataProvider
     */
    public function testCreateImage(array $layers, array $options, $outputName)
    {
        $image = $this->factory->createImage($layers, $options);
        file_put_contents(__DIR__.'/TestOutput/'.$outputName, $image->getContents());
    }

    public function layerDataProvider()
    {
        $extractor = new GifExtractor();

        $outputName1 = 'gif_factory_image_should_be_animated_01.gif';
        $options1    = [];
        $layers1[0]  = new BackgroundLayer();
        $layers1[0]->add([
            'gif.extracted'       => $extractor->extractFromStream(__DIR__.'/../../../../Fixtures/gif_89a_palette_alpha_animated_339x473.gif'),
            'image.width'         => 339,
            'image.height'        => 473,
            'image.resize.width'  => 400,
            'image.resize.height' => 400,
            'image.resize.option' => ImageAwareLayerInterface::RESIZE_FILL_CROP,
            'final.width'         => 400,
            'final.height'        => 400,
        ]);
        $layers1[1] = new ImageLayer();
        $layers1[1]->add([
            'image.imc_uri'        => __DIR__.'/../../../../Fixtures/webp_vp8_lossy_truecolor_550x368.webp',
            'image.width'          => 550,
            'image.height'         => 368,
            'image.resize.width'   => 50,
            'image.resize.height'  => 50,
            'image.resize.option'  => ImageAwareLayerInterface::RESIZE_FILL_CROP,
            'image.format'         => PhpGdContext::FORMAT_WEBP,
            'final.width'          => 50,
            'final.height'         => 50,
            'regular.move.x'       => 10,
            'regular.move.y'       => 10,
            'regular.move.gravity' => RegularLayerInterface::MOVE_TOP_LEFT,
        ]);
        $layers1[2] = new TextLayer();
        $layers1[2]->add([
            'text.font.filename'   => __DIR__.'/../../../../Fixtures/pfa_truecolor_alpha.pfa',
            'text.font.size'       => 12,
            'text.font.rgb_color'  => [255, 255, 255],
            'text.label'           => 'Hello World',
            'text.angle'           => 90,
            'text.lineSpacing'     => 1,
            'text.box.paddings'    => [0, 0, 0, 0],
            'regular.move.x'       => 0,
            'regular.move.y'       => 0,
            'regular.move.gravity' => RegularLayerInterface::MOVE_CENTER,
        ]);

        $outputName2 = 'gif_factory_image_should_be_animated_02.gif';
        $options2    = [];
        $layers2[0]  = new BackgroundLayer();
        $layers2[0]->add([
            'gif.extracted'       => $extractor->extractFromStream(__DIR__.'/../../../../Fixtures/gif_89a_palette_animated_375x225.gif'),
            'final.width'         => 300,
            'final.height'        => 180,
            'image.width'         => 339,
            'image.height'        => 473,
            'image.resize.width'  => 300,
            'image.resize.height' => 180,
            'image.resize.option' => ImageAwareLayerInterface::RESIZE_SHRINK,
        ]);
        $layers2[1] = new ImageLayer();
        $layers2[1]->add([
            'image.imc_uri'        => __DIR__.'/../../../../Fixtures/jpeg_exif_truecolor_480x360.jpg',
            'image.width'          => 480,
            'image.height'         => 360,
            'image.resize.width'   => 100,
            'image.resize.height'  => 100,
            'image.resize.option'  => ImageAwareLayerInterface::RESIZE_FILL_CROP,
            'image.format'         => PhpGdContext::FORMAT_JPEG,
            'final.width'          => 100,
            'final.height'         => 100,
            'regular.move.x'       => 10,
            'regular.move.y'       => 10,
            'regular.move.gravity' => RegularLayerInterface::MOVE_TOP_LEFT,
        ]);
        $layers2[2] = new TextLayer();
        $layers2[2]->add([
            'text.font.filename'   => __DIR__.'/../../../../Fixtures/pfa_truecolor_alpha.pfa',
            'text.font.size'       => 12,
            'text.font.rgb_color'  => [255, 255, 255],
            'text.label'           => 'Hello World',
            'text.angle'           => 30,
            'text.lineSpacing'     => 1,
            'text.box.paddings'    => [0, 0, 0, 0],
            'regular.move.x'       => 0,
            'regular.move.y'       => 0,
            'regular.move.gravity' => RegularLayerInterface::MOVE_CENTER,
        ]);

        return [
            [$layers1, $options1, $outputName1],
            [$layers2, $options2, $outputName2],
        ];
    }
}
