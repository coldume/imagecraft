<?php

namespace Imagecraft\Layer;

/**
 * @author Xianghan Wang <coldume@gmail.com>
 * @since  1.0.0
 */
interface ImageAwareLayerInterface extends LayerInterface
{
    const RESIZE_SHRINK    = 'shrink';
    const RESIZE_FILL_CROP = 'fill_crop';

    /**
     * @param  string    $url
     * @param  int|float $dataLimit
     * @param  int|float $timeout
     * @return $this
     * @api
     */
    public function http($url, $dataLimit = -1, $timeout = -1);

    /**
     * @param  string $filename
     * @return $this
     * @api
     */
    public function filename($filename);

    /**
     * @param  string $contents
     * @return $this
     * @api
     */
    public function contents($contents);

    /**
     * @param  int    $width
     * @param  int    $height
     * @param  string $option
     * @return $this
     * @api
     */
    public function resize($width, $height, $option = self::RESIZE_SHRINK);
}
