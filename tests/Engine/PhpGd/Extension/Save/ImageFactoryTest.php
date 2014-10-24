<?php

namespace Imagecraft\Engine\PhpGd\Extension\Save;

use Imagecraft\Layer\BackgroundLayer;
use Imagecraft\Engine\PhpGd\PhpGdContext;

/**
 * @requires extension gd
 * @requires function imagegif
 * @requires function imagecreatefromgif
 * @requires function imagecreatefromwebp
 * @requires function imagecreatefromjpeg
 * @requires function imagecreatefrompng
 * @covers   Imagecraft\Engine\PhpGd\Extension\Save\ImageFactory
 */
class ImageFactoryTest extends \PHPUnit_Framework_TestCase
{
    protected $factory;

    public function setUp()
    {
        $rh            = $this->getMock('Imagecraft\\Engine\\PhpGd\\Helper\\ResourceHelper', null);
        $this->factory = $this->getMock('Imagecraft\\Engine\\PhpGd\\Extension\\Save\\ImageFactory', null, [$rh]);
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
        $outputName1 = 'image_factory_image_should_be_valid_01.gif';
        $options1    = [];
        $layers1     = [];
        $layers1[0] = new BackgroundLayer();
        $layers1[0]->add([
            'image.imc_uri'    => __DIR__.'/../../../../Fixtures/gif_89a_palette_alpha_animated_339x473.gif',
            'image.format' => PhpGdContext::FORMAT_GIF,
        ]);

        $outputName2 = 'image_factory_image_should_be_valid_02.jpg';
        $options2    = [];
        $layers2     = [];
        $layers2[0] = new BackgroundLayer();
        $layers2[0]->add([
            'image.imc_uri'    => __DIR__.'/../../../../Fixtures/jpeg_exif_truecolor_480x360.jpg',
            'image.format' => PhpGdContext::FORMAT_JPEG,
        ]);

        return [
            [$layers1, $options1, $outputName1],
            [$layers2, $options2, $outputName2],
        ];
    }
}
