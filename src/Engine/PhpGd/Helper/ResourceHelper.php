<?php

namespace Imagecraft\Engine\PhpGd\Helper;

use Imagecraft\Exception\InvalidImageException;
use Imagecraft\Exception\InvalidFontException;
use Imagecraft\Layer\ImageAwareLayerInterface;;
use Imagecraft\Layer\RegularLayerInterface;
use Imagecraft\Engine\PhpGd\PhpGdContext;

/**
 * @author Xianghan Wang <coldume@gmail.com>
 * @since  1.0.0
 */
class ResourceHelper
{
    /**
     * @param  int $width
     * @param  int $height
     * @return resource
     */
    public function getEmptyGdResource($width, $height)
    {
        $resource = imagecreatetruecolor($width, $height);
        imagesavealpha($resource, true);
        imagealphablending($resource, true);
        $trans = imagecolorallocatealpha($resource, 255, 255, 255, 127);
        imagefill($resource, 0, 0, $trans);
        imagecolortransparent($resource, $trans);

        return $resource;
    }

    /**
     * @param  resource $resource
     * @return resource
     */
    public function getPalettizedGdResource($resource)
    {
        imagetruecolortopalette($resource, true, 255);
        if (-1 == $trans = imagecolortransparent($resource)) {
            $trans = imagecolorallocate($resource, 255, 255, 255);
            imagecolortransparent($resource, $trans);
        }

        return $resource;
    }

    /**
     * @param  string $format
     * @param  string $uri
     * @param  bool   $throw
     * @return resource|false
     * @throws InvalidImageException
     */
    public function getGdResourceFromStream($format, $uri, $throw = false)
    {
        switch ($format) {
            case PhpGdContext::FORMAT_WEBP:
                $resource = @imagecreatefromwebp($uri);
                break;
            case PhpGdContext::FORMAT_PNG:
                $resource = @imagecreatefrompng($uri);
                break;
            case PhpGdContext::FORMAT_JPEG:
                $resource = @imagecreatefromjpeg($uri);
                break;
            case PhpGdContext::FORMAT_GIF:
                $resource = @imagecreatefromgif($uri);
                break;
            default:
                $resource = false;
        }

        if (!$resource && $throw) {
            throw new InvalidImageException('image.corrupted');
        }

        return $resource;
    }

    /**
     * @param  string $format
     * @param  string $contents
     * @param  bool   $throw
     * @return resource
     * @throws InvalidImageException
     */
    public function getGdResourceFromContents($format, $contents, $throw = false)
    {
        if (PhpGdContext::FORMAT_WEBP === $format) {
            $resource = @imagecreatefromwebp('data://image/webp;base64,'.base64_encode($contents));
        } else {
            $resource = @imagecreatefromstring($contents);
        }

        if (!$resource && $throw) {
            throw new InvalidImageException('image.corrupted');
        }

        return $resource;
    }

    /**
     * @param  string       $format
     * @param  resource     $resource
     * @param  mixed[]|null $options
     * @param  bool         $throw
     * @return string|false
     * @throws InvalidImageException
     */
    public function getContentsFromGdResource($format, $resource, array $options = [], $throw = false)
    {
        ob_start();
        switch ($format) {
            case PhpGdContext::FORMAT_WEBP:
                $success = @imagewebp($resource);
                break;
            case PhpGdContext::FORMAT_PNG:
                if (isset($options['png_compression'])) {
                    $compression = round(($options['png_compression'] * 9 / 100));
                } else {
                    $compression = 9;
                }
                $success = @imagepng($resource, null, $compression, PNG_ALL_FILTERS);
                break;
            case PhpGdContext::FORMAT_JPEG:
                $quality = isset($options['jpeg_quality']) ? $options['jpeg_quality'] : 100;
                $success = @imagejpeg($resource, null, $quality);
                break;
            case PhpGdContext::FORMAT_GIF:
                if (!imageistruecolor($resource)) {
                    $resource = $this->getPalettizedGdResource($resource);
                }
                $success = @imagegif($resource);
                break;
            default:
                $success = false;
        }
        $contents = ob_get_clean();

        if (!$success && $throw) {
            throw new InvalidImageException('image.process.error');
        }

        return ('' === $contents) ? false : $contents;
    }

    /**
     * @param  int    $originalWidth
     * @param  int    $originalHeight
     * @param  int    $resizeWidth
     * @param  int    $resizeHeight
     * @param  string $resizeOption
     * @return int[]|false
     */
    public function getResizeArguments(
        $originalWidth,
        $originalHeight,
        $resizeWidth,
        $resizeHeight,
        $resizeOption
    ) {
        if ($originalWidth === $resizeWidth && $originalHeight === $resizeHeight) {
            return false;
        }
        switch ($resizeOption) {
            case ImageAwareLayerInterface::RESIZE_SHRINK:
                $srcX = 0;
                $srcY = 0;
                $srcW = $originalWidth;
                $srcH = $originalHeight;
                $dstX = 0;
                $dstY = 0;
                if ($originalWidth <= $resizeWidth && $originalHeight <= $resizeHeight) {
                    return false;
                }
                if ($originalWidth / $originalHeight >= $resizeWidth / $resizeHeight) {
                    $dstW = $resizeWidth;
                    $dstH = round(($resizeWidth * $originalHeight) / $originalWidth) ?: 1;
                } else {
                    $dstW = round(($resizeHeight * $originalWidth) / $originalHeight) ?: 1;
                    $dstH = $resizeHeight;
                }
                break;
            case ImageAwareLayerInterface::RESIZE_FILL_CROP:
                $dstX = 0;
                $dstY = 0;
                $dstW = $resizeWidth;
                $dstH = $resizeHeight;
                if ($originalWidth / $originalHeight >= $resizeWidth / $resizeHeight) {
                    $srcW = round(($resizeWidth * $originalHeight) / $resizeHeight) ?: 1;
                    $srcH = $originalHeight;
                    $srcX = round(($originalWidth - $srcW) / 2);
                    $srcY = 0;
                } else {
                    $srcW = $originalWidth;
                    $srcH = round(($originalWidth * $resizeHeight) / $resizeWidth) ?: 1;
                    $srcX = 0;
                    $srcY = round(($originalHeight - $srcH) / 2);
                }
        }

        return [
            'dst_x' => $dstX, 'dst_y' => $dstY, 'dst_w' => $dstW, 'dst_h' => $dstH,
            'src_x' => $srcX, 'src_y' => $srcY, 'src_w' => $srcW, 'src_h' => $srcH,
        ];
    }

    /**
     * @param  resource $srcResource
     * @param  int      $resizeWidth
     * @param  int      $resizeHeight
     * @param  string   $resizeOption
     * @param  bool     $resample
     * @return resource
     */
    public function getResizedGdResource(
        $srcResource,
        $resizeWidth,
        $resizeHeight,
        $resizeOption,
        $resample = true
    ) {
        $originalWidth = imagesx($srcResource);
        $originalHeight = imagesy($srcResource);
        $args = $this->getResizeArguments(
            $originalWidth,
            $originalHeight,
            $resizeWidth,
            $resizeHeight,
            $resizeOption
        );

        if (!$args) {
            return $srcResource;
        }

        $dstResource = $this->getEmptyGdResource($args['dst_w'], $args['dst_h']);

        if ($resample) {
            imagecopyresampled(
                $dstResource, $srcResource,
                $args['dst_x'], $args['dst_y'], $args['src_x'], $args['src_y'],
                $args['dst_w'], $args['dst_h'], $args['src_w'], $args['src_h']
            );
        } else {
            imagecopyresized(
                $dstResource, $srcResource,
                $args['dst_x'], $args['dst_y'], $args['src_x'], $args['src_y'],
                $args['dst_w'], $args['dst_h'], $args['src_w'], $args['src_h']
            );
        }
        imagedestroy($srcResource);

        return $dstResource;
    }

    /**
     * @param  resource $dstResource
     * @param  resource $srcResource
     * @param  int      $x
     * @param  int      $y
     * @param  string   $gravity
     * @return resource
     */
    public function getMergedGdResource(
        $dstResource,
        $srcResource,
        $x = 0,
        $y = 0,
        $gravity = RegularLayerInterface::MOVE_TOP_LEFT
    ) {
        $dstWidth  = imagesx($dstResource);
        $dstHeight = imagesy($dstResource);
        $srcWidth  = imagesx($srcResource);
        $srcHeight = imagesY($srcResource);

        switch ($gravity) {
            case RegularLayerInterface::MOVE_TOP_LEFT;
                $x += 0;
                $y += 0;
                break;
            case RegularLayerInterface::MOVE_TOP_CENTER:
                $x += round(($dstWidth - $srcWidth) / 2);
                $y += 0;
                break;
            case RegularLayerInterface::MOVE_TOP_RIGHT:
                $x += $dstWidth - $srcWidth;
                $y += 0;
                break;
            case RegularLayerInterface::MOVE_CENTER_LEFT:
                $x += 0;
                $y += round(($dstHeight - $srcHeight) / 2);
                break;
            case RegularLayerInterface::MOVE_CENTER:
                $x += round(($dstWidth - $srcWidth) / 2);
                $y += round(($dstHeight - $srcHeight) / 2);
                break;
            case RegularLayerInterface::MOVE_CENTER_RIGHT:
                $x += $dstWidth - $srcWidth;
                $y += round(($dstHeight - $srcHeight) / 2);
                break;
            case RegularLayerInterface::MOVE_BOTTOM_LEFT:
                $x += 0;
                $y += $dstHeight - $srcHeight;
                break;
            case RegularLayerInterface::MOVE_BOTTOM_CENTER:
                $x += round(($dstWidth - $srcWidth) / 2);
                $y += $dstHeight - $srcHeight;
                break;
            case RegularLayerInterface::MOVE_BOTTOM_RIGHT:
                $x += $dstWidth - $srcWidth;
                $y += $dstHeight - $srcHeight;
        }

        if (
           $x <= -$srcWidth  || $x >= $dstWidth  ||
           $y <= -$srcHeight || $y >= $dstHeight
        ) {
            return $dstResource;
        }

        if ($x <= 0) {
            $dstX = 0;
            $srcX = -$x;
            $srcW = min(($srcWidth + $x), $dstWidth);
        } else {
            $dstX = $x;
            $srcX = 0;
            $srcW = min(($dstWidth - $x), $srcWidth);
        }

        if ($y <= 0) {
            $dstY = 0;
            $srcY = -$y;
            $srcH = min(($srcHeight + $y), $dstHeight);
        } else {
            $dstY = $y;
            $srcY = 0;
            $srcH = min(($dstHeight - $y), $srcHeight);
        }
        if (!imageistruecolor($dstResource)) {
            $resource = $this->getEmptyGdResource($dstWidth, $dstHeight);
            imagecopy($resource, $dstResource, 0, 0, 0, 0, $dstWidth, $dstHeight);
            imagedestroy($dstResource);
            $dstResource = $resource;
        }
        imagecopy($dstResource, $srcResource, $dstX, $dstY, $srcX, $srcY, $srcW, $srcH);

        return $dstResource;
    }

    /**
     * @param  resource $srcResource
     * @return resource
     */
    public function getClonedGdResource($srcResource)
    {
        $width = imagesx($srcResource);
        $height = imagesy($srcResource);
        $dstResource = $this->getEmptyGdResource($width, $height);
        $dstResource = $this->getMergedGdResource($dstResource, $srcResource);
        if (!imageistruecolor($srcResource)) {
            $dstResource = $this->getPalettizedGdResource($dstResource);
        }

        return $dstResource;
    }

    /**
     * @param  string     $fontUri
     * @param  int        $fontSize
     * @param  int[]      $fontColor
     * @param  string     $label
     * @param  float|int  $lineSpacing
     * @param  int        $angle
     * @param  int[]      $blockPaddings
     * @param  null|int[] $blockColor
     * @return resource
     */
    public function getTextGdResource(
        $fontUri,
        $fontSize,
        array $fontColor,
        $label,
        $lineSpacing,
        $angle,
        array $blockPaddings = [0, 0, 0, 0],
        array $blockColor = null
    ) {
        $points = $this->getTextPoints(
            $fontUri,
            $fontSize,
            $label,
            $lineSpacing,
            $angle,
            $blockPaddings
        );
        $width  = max(
            abs($points['x'][0] - $points['x'][2]),
            abs($points['x'][1] - $points['x'][3])
        );
        $height = max(
            abs($points['y'][0] - $points['y'][2]),
            abs($points['y'][1] - $points['y'][3])
        );
        $resource = $this->getEmptyGdResource($width, $height);
        if (null !== $blockColor) {
            $blockColor = imagecolorallocate($resource, $blockColor[0], $blockColor[1], $blockColor[2]);
            if (function_exists('imageantialias')) {
                imageantialias($resource, true);
            }
            $coordinates = [
                $points['x'][0], $points['y'][0], $points['x'][1], $points['y'][1],
                $points['x'][2], $points['y'][2], $points['x'][3], $points['y'][3],
            ];
            imagefilledpolygon($resource, $coordinates, 4, $blockColor);
        }
        $fontColor = imagecolorallocate($resource, $fontColor[0], $fontColor[1], $fontColor[2]);
        imagefttext(
            $resource,
            $fontSize,
            $angle,
            $points['x'][4],
            $points['y'][4],
            $fontColor,
            $fontUri,
            $label,
            ['linespacing' => $lineSpacing]
        );

        return $resource;
    }

    /**
     * @param  string    $fontUri
     * @param  int       $fontSize
     * @param  string    $label
     * @param  float|int $lineSpacing
     * @param  int       $angle
     * @param  int[]     $blockPaddings
     * @return int[]
     * @throws InvalidFontException
     */
    protected function getTextPoints(
        $fontUri,
        $fontSize,
        $label,
        $lineSpacing,
        $angle,
        array $blockPaddings
    ) {
        $ftbbox = @imageftbbox($fontSize, 0, $fontUri, $label, ['linespacing' => $lineSpacing]);
        if (false === $ftbbox) {
            throw new InvalidFontException('text.adding.error');
        }
        $radian  = deg2rad($angle);
        $width   = abs($ftbbox[0] - $ftbbox[2]) + $blockPaddings[1] + $blockPaddings[3];
        $width  += ceil($fontSize/5);
        $height  = abs($ftbbox[1] - $ftbbox[7]) + $blockPaddings[0] + $blockPaddings[2];
        $height += ceil($fontSize/5);
        $w       = $width / 2;
        $h       = $height / 2;

        $radius = sqrt(pow($width, 2) + pow($height, 2)) / 2;
        $points = [];
        for ($i = 0; $i < 4; $i++) {
            $a = (0 === $i || 1 === $i) ? $h : -$h;
            $b = (1 === $i || 2 === $i) ? $w  : -$w;
            $points['x'][$i] = cos(atan2($a, $b) - $radian) * $radius + $w;
            $points['y'][$i] = sin(atan2($a, $b) - $radian) * $radius + $h;
        }
        $x = min($points['x']);
        $y = min($points['y']);
        for ($i = 0; $i < 4; $i++) {
            $points['x'][$i] = round($points['x'][$i] - $x);
            $points['y'][$i] = round($points['y'][$i] - $y);
        }

        $a = $blockPaddings[3];
        $b = $blockPaddings[0] - $ftbbox[7];
        $radius  = sqrt(pow(abs($a - $w), 2) + pow(abs($b - $h), 2));
        $points['x'][4] = round(cos(atan2($b - $h, $a - $w) - $radian) * $radius + $w - $x);
        $points['y'][4] = round(sin(atan2($b - $h, $a - $w) - $radian) * $radius + $h - $y);

        return $points;
    }
}
