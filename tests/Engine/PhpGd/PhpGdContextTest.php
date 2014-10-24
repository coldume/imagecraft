<?php

namespace Imagecraft\Engine\PhpGd;

/**
 * @requires extension gd
 * @requires function imagegif
 * @requires function imagecreatefromgif
 * @requires function imagecreatefromwebp
 * @requires function imagecreatefromjpeg
 * @requires function imagecreatefrompng
 * @requires function imagefttext
 * @covers   Imagecraft\Engine\PhpGd\PhpGdContext
 */
class PhpGdContextTest extends \PHPUnit_Framework_TestCase
{
    protected $context;

    public function setUp()
    {
        $this->context = $this->getMock('Imagecraft\\Engine\\PhpGd\\PhpGdContext', null);
    }

    public function testIsImageFormatSupported()
    {
        $this->assertTrue($this->context->isImageFormatSupported(PhpGdContext::FORMAT_WEBP));
        $this->assertTrue($this->context->isImageFormatSupported(PhpGdContext::FORMAT_PNG));
        $this->assertTrue($this->context->isImageFormatSupported(PhpGdContext::FORMAT_JPEG));
        $this->assertTrue($this->context->isImageFormatSupported(PhpGdContext::FORMAT_GIF));
        $this->assertFalse($this->context->isImageFormatSupported('foo'));
    }

    public function testGetSupportedImageFormatsToString()
    {
        $this->assertEquals('"WEBP (VP8)", "PNG", "JPEG", "GIF"', $this->context->getSupportedImageFormatsToString());
    }

    public function testGetImageMime()
    {
        $this->assertEquals('image/webp', $this->context->getImageMime(PhpGdContext::FORMAT_WEBP));
    }

    public function testGetImageExtension()
    {
        $this->assertEquals('webp', $this->context->getImageExtension(PhpGdContext::FORMAT_WEBP));
    }

    public function testIsEngineSupported()
    {
        $this->assertTrue($this->context->isEngineSupported());
    }

    public function testIsFreeTypeSupported()
    {
        $this->assertTrue($this->context->isFreeTypeSupported());
    }

    public function testGetSupportedFontFormatsToString()
    {
        $this->assertInternalType('string', $this->context->getSupportedFontFormatsToString());
    }
}
