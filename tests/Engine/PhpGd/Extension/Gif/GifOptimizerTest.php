<?php

namespace Imagecraft\Engine\PhpGd\Extension\GifAnimation;

/**
 * @requires extension gd
 * @covers   Imagecraft\Engine\PhpGd\Extension\Gif\GifOptimizer
 */
class GifOptimizerTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @dataProvider imageDataProvider
     */
    public function testOptimizedGdResource($image, $outputName)
    {
        $rh        = $this->getMock('Imagecraft\\Engine\\PhpGd\\Helper\\ResourceHelper', null);
        $optimizer = $this->getMock('Imagecraft\\Engine\\PhpGd\\Extension\\Gif\\GifOptimizer', null, [$rh]);
        $resource1 = imagecreatefromjpeg($image);
        $resource2 = imagecreatefromjpeg($image);
        $resource  = $optimizer->getOptimizedGdResource($resource1, $resource2);
        imagegif($resource, __DIR__.'/TestOutput/'.$outputName);
        imagedestroy($resource);
        imagedestroy($resource2);
    }

    public function imageDataProvider()
    {
        return [
            [__DIR__.'/../../../../Fixtures/jpeg_exif_truecolor_480x360.jpg', 'gif_optimizer_image_should_be_blank_01.gif'],
        ];
    }
}
