<?php

namespace Imagecraft;

/**
 * @covers Imagecraft\ImageBuilder
 */
class ImageBuilderTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @requires extension gd
     * @requires function imagegif
     * @requires function imagecreatefromgif
     * @requires function imagecreatefromwebp
     * @requires function imagecreatefromjpeg
     * @requires function imagecreatefrompng
     * @requires function imagefttext
     */
    public function testBuilderImageWhenEngineIsPhpGd()
    {
        $builder = $this->getMock('Imagecraft\\ImageBuilder', null, [['engine' => 'php_gd']]);
        $context = $builder->about();
        $this->assertInstanceOf('Imagecraft\\AbstractContext', $context);
        $image = $builder
            ->addBackgroundLayer()
                ->filename(__DIR__.'/Fixtures/jpeg_exif_truecolor_480x360.jpg')
                ->resize(400, 400, 'shrink')
                ->done()
            ->addImageLayer()
                ->filename(__DIR__.'/Fixtures/gif_87a_palette_250x297.gif')
                ->resize(100, 100)
                ->move(100, 100, 'top_left')
                ->done()
            ->addTextLayer()
                ->font(__DIR__.'/Fixtures/pfa_truecolor_alpha.pfa', 12, '#FFF')
                ->label('Hello World')
                ->box([0, 0, 0, 0], '#000')
                ->done()
            ->save()
        ;
        $this->assertTrue($image->isValid());
    }
}
