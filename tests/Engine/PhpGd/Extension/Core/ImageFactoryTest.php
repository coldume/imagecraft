<?php

namespace Imagecraft\Engine\PhpGd\Extension\Core;

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
 * @covers   Imagecraft\Engine\PhpGd\Extension\Core\ImageFactory
 */
class ImageFactoryTest extends \PHPUnit_Framework_TestCase
{
    protected $factory;

    public function setUp()
    {
        $rh            = $this->getMock('Imagecraft\\Engine\\PhpGd\\Helper\\ResourceHelper', null);
        $this->factory = $this->getMock('Imagecraft\\Engine\\PhpGd\\Extension\\Core\\ImageFactory', null, [$rh]);
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
        $outputName1 = 'image_factory_image_should_be_valid_01.png';
        $options1    = ['png_quality' => 100, 'jpeg_compression' => 100];
        $layers1     = [];
        $layers1[0] = new BackgroundLayer();
        $layers1[0]->add([
            'image.imc_uri'       => __DIR__.'/../../../../Fixtures/gif_89a_palette_alpha_animated_339x473.gif',
            'image.format'        => PhpGdContext::FORMAT_GIF,
            'image.width'         => 339,
            'image.height'        => 473,
            'image.resize.width'  => 500,
            'image.resize.height' => 500,
            'image.resize.option' => ImageAwareLayerInterface::RESIZE_FILL_CROP,
            'final.width'         => 500,
            'final.height'        => 500,
            'final.format'        => PhpGdContext::FORMAT_PNG,
        ]);
        $layers1[1] = new ImageLayer();
        $layers1[1]->add([
            'image.contents'       => file_get_contents(__DIR__.'/../../../../Fixtures/gif_89a_palette_animated_375x225.gif'),
            'image.width'          => 375,
            'image.height'         => 225,
            'image.format'         => PhpGdContext::FORMAT_GIF,
            'image.resize.width'   => 50,
            'image.resize.height'  => 50,
            'image.resize.option'  => ImageAwareLayerInterface::RESIZE_FILL_CROP,
            'regular.move.x'       => 10,
            'regular.move.y'       => 10,
            'regular.move.gravity' => RegularLayerInterface::MOVE_TOP_LEFT,
        ]);
        $layers1[2] = new TextLayer();
        $layers1[2]->add([
            'text.font.filename'   => __DIR__.'/../../../../Fixtures/pfa_truecolor_alpha.pfa',
            'text.font.size'       => 25,
            'text.font.rgb_color'  => [255, 255, 255],
            'text.label'           => 'Hello World',
            'text.angle'           => 30,
            'text.lineSpacing'     => 1,
            'text.box.paddings'    => [0, 0, 0, 0],
            'regular.move.x'       => 0,
            'regular.move.y'       => 0,
            'regular.move.gravity' => RegularLayerInterface::MOVE_CENTER,
        ]);

        $outputName2 = 'image_factory_image_should_be_valid_02.gif';
        $options2    = ['png_quality' => 100, 'jpeg_compression' => 100];
        $layers2     = [];
        $layers2[0] = new BackgroundLayer();
        $layers2[0]->add([
            'image.imc_uri'       => __DIR__.'/../../../../Fixtures/jpeg_exif_truecolor_480x360.jpg',
            'image.format'        => PhpGdContext::FORMAT_JPEG,
            'image.width'         => 480,
            'image.height'        => 360,
            'image.resize.width'  => 432,
            'image.resize.height' => 324,
            'image.resize.option' => ImageAwareLayerInterface::RESIZE_SHRINK,
            'final.width'         => 432,
            'final.height'        => 324,
            'final.format'        => PhpGdContext::FORMAT_GIF,
        ]);
        $layers2[1] = new ImageLayer();
        $layers2[1]->add([
            'image.contents'       => file_get_contents(__DIR__.'/../../../../Fixtures/webp_vp8_lossy_truecolor_550x368.webp'),
            'image.width'          => 550,
            'image.height'         => 368,
            'image.format'         => PhpGdContext::FORMAT_WEBP,
            'image.resize.width'   => 50,
            'image.resize.height'  => 50,
            'image.resize.option'  => ImageAwareLayerInterface::RESIZE_FILL_CROP,
            'regular.move.x'       => 10,
            'regular.move.y'       => 10,
            'regular.move.gravity' => RegularLayerInterface::MOVE_TOP_LEFT,
        ]);
        $layers2[2] = new TextLayer();
        $layers2[2]->add([
            'text.font.filename'   => __DIR__.'/../../../../Fixtures/pfa_truecolor_alpha.pfa',
            'text.font.size'       => 25,
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
