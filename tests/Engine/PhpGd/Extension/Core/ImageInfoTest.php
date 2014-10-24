<?php

namespace Imagecraft\Engine\PhpGd\Extension\Core;

use Imagecraft\Engine\PhpGd\PhpGdContext;

/**
 * @covers Imagecraft\Engine\PhpGd\Extension\Core\ImageInfo
 */
class ImageInfoTest extends \PHPUnit_Framework_TestCase
{
    protected $context;

    protected $info;

    public function setUp()
    {
        $this->context = $this->getMock('Imagecraft\\Engine\\PhpGd\\PhpGdContext', []);
        $this->info    = $this->getMock('Imagecraft\\Engine\\PhpGd\\Extension\\Core\\ImageInfo', null, [$this->context]);
    }

    /**
     * @dataProvider imageDataProvider
     */
    public function testResolveFromFilePointer($filename, $format, $width, $height)
    {
        $this->context
            ->expects($this->atLeastOnce())
            ->method('isImageFormatSupported')
            ->will($this->returnValue(true))
        ;
        $fp = fopen($filename, 'rb');
        $info = $this->info->resolveFromFilePointer($fp);
        $this->assertEquals($format, $info['format']);
        $this->assertEquals($width, $info['width']);
        $this->assertEquals($height, $info['height']);
    }

    /**
     * @depends      testResolveFromFilePointer
     * @dataProvider imageDataProvider
     */
    public function testResolveFromStream($filename, $format, $width, $height)
    {
        $this->context
            ->expects($this->atLeastOnce())
            ->method('isImageFormatSupported')
            ->will($this->returnValue(true))
        ;
        $info = $this->info->resolveFromStream($filename);
        $this->assertEquals($format, $info['format']);
        $this->assertEquals($width, $info['width']);
        $this->assertEquals($height, $info['height']);
    }

    /**
     * @depends      testResolveFromFilePointer
     * @dataProvider imageDataProvider
     */
    public function testResolveFromContents($filename, $format, $width, $height)
    {
        $this->context
            ->expects($this->atLeastOnce())
            ->method('isImageFormatSupported')
            ->will($this->returnValue(true))
        ;
        $info = $this->info->resolveFromContents(file_get_contents($filename));
        $this->assertEquals($format, $info['format']);
        $this->assertEquals($width, $info['width']);
        $this->assertEquals($height, $info['height']);
    }

    /**
     * @requires          extension fileinfo
     * @depends           testResolveFromFilePointer
     * @expectedException Imagecraft\Exception\InvalidImageException
     */
    public function testResolveInvalidImage()
    {
        $this->context
            ->expects($this->atLeastOnce())
            ->method('isFileinfoExtensionEnabled')
            ->will($this->returnValue(true))
        ;
        $fp = fopen(__FILE__, 'r');
        $this->info->resolveFromFilePointer($fp);
    }

    /**
     * @depends           testResolveFromFilePointer
     * @dataProvider      invalidWebpDataProvider
     * @expectedException Imagecraft\Exception\InvalidImageException
     */
    public function testResolveInvalidWebp($filename)
    {
        $this->info->resolveFromFilePointer(fopen($filename, 'rb'));
    }

    public function imageDataProvider()
    {
        return [
            [__DIR__.'/../../../../Fixtures/webp_vp8_lossy_truecolor_550x368.webp', PhpGdContext::FORMAT_WEBP, 550,  368],
            [__DIR__.'/../../../../Fixtures/gif_87a_palette_250x297.gif',           PhpGdContext::FORMAT_GIF,  250,  297],
            [__DIR__.'/../../../../Fixtures/png_truecolor_alpha_300x395.png',       PhpGdContext::FORMAT_PNG,  300,  395],
            [__DIR__.'/../../../../Fixtures/png_palette_alpha_3000x1174.png',       PhpGdContext::FORMAT_PNG,  3000, 1174],
            [__DIR__.'/../../../../Fixtures/jpeg_jfjf_truecolor_1920x758.jpg',      PhpGdContext::FORMAT_JPEG, 1920, 758],
            [__DIR__.'/../../../../Fixtures/jpeg_jfjf_grayscale_480x361.jpg',       PhpGdContext::FORMAT_JPEG, 480,  361],
            [__DIR__.'/../../../../Fixtures/jpeg_exif_truecolor_480x360.jpg',       PhpGdContext::FORMAT_JPEG, 480,  360],
            [__DIR__.'/../../../../Fixtures/jpeg_jfjf_sos_truecolor_1920x1200.jpg', PhpGdContext::FORMAT_JPEG, 1920, 1200],
        ];
    }

    public function invalidWebpDataProvider()
    {
        return [
            [__DIR__.'/../../../../Fixtures/webp_vp8l_lossless_truecolor_alpha_800x600.webp'],
            [__DIR__.'/../../../../Fixtures/webp_vp8x_lossy_truecolor_alpha_421x163.webp'],
        ];
    }
}
