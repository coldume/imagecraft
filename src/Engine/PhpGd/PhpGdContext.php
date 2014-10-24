<?php

namespace Imagecraft\Engine\PhpGd;

use Imagecraft\AbstractContext;

/**
 * WBMP and XBM are not supported as they have no magic number.
 * XPM is not supported either as GD has no XPM output functions.
 *
 * @author Xianghan Wang <coldume@gmail.com>
 * @since  1.0.0
 */
class PhpGdContext extends AbstractContext
{
    const FORMAT_WEBP = 'webp';
    const FORMAT_PNG  = 'png';
    const FORMAT_JPEG = 'jpeg';
    const FORMAT_GIF  = 'gif';

    /**
     * @param  string $format
     * @return bool
     */
    public function isImageFormatSupported($format)
    {
        switch ($format) {
            case static::FORMAT_WEBP:
                $supported = function_exists('imagecreatefromwebp');
                break;
            case static::FORMAT_PNG:
                $supported = function_exists('imagecreatefrompng');
                break;
            case static::FORMAT_JPEG:
                $supported = function_exists('imagecreatefromjpeg');
                break;
            case static::FORMAT_GIF:
                $supported = function_exists('imagecreatefromgif') && function_exists('imagegif');
                break;
            default:
                $supported = false;
        }

        return $supported;
    }

    /**
     * @param  string $format
     * @return string
     */
    public function getImageMime($format)
    {
        $mimes = [
            static::FORMAT_WEBP => 'image/webp',
            static::FORMAT_PNG  => 'image/png',
            static::FORMAT_JPEG => 'image/jpeg',
            static::FORMAT_GIF  => 'image/gif',
        ];

        return $mimes[$format];
    }

    /**
     * @param  string $format
     * @return string
     */
    public function getImageExtension($format)
    {
        $extensions = [
            static::FORMAT_WEBP => 'webp',
            static::FORMAT_PNG  => 'png',
            static::FORMAT_JPEG => 'jpg',
            static::FORMAT_GIF  => 'gif',
        ];

        return $extensions[$format];
    }

    /**
     * @return bool
     */
    public function isFreeTypeSupported()
    {
        return function_exists('imagefttext');
    }

    /**
     * @inheritDoc
     */
    public function isEngineSupported()
    {
        return extension_loaded('gd');
    }

    /**
     * @inheritDoc
     */
    public function getSupportedImageFormatsToString()
    {
        $formats = [
            [static::FORMAT_WEBP, 'WEBP (VP8)'],
            [static::FORMAT_PNG,  'PNG'],
            [static::FORMAT_JPEG, 'JPEG'],
            [static::FORMAT_GIF,  'GIF'],
        ];
        for ($i = 0, $str = ''; $i < count($formats); $i++) {
            if ($this->isImageFormatSupported($formats[$i][0])) {
                $str .= (0 == $i) ? '"' : ', "';
                $str .= $formats[$i][1].'"';
            }
        }

        return $str;
    }

    /**
     * @inheritDoc
     */
    public function getSupportedFontFormatsToString()
    {
        if ($this->isFreeTypeSupported()) {
            return '"Postscript (.pfa, .pfb)", "TureType (.ttf)", "OpenType (.otf)"';
        }

        return '';
    }
}
