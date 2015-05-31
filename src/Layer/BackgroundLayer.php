<?php

namespace Imagecraft\Layer;

/**
 * @author Xianghan Wang <coldume@gmail.com>
 * @since  1.0.0
 */
class BackgroundLayer extends AbstractLayer implements BackgroundLayerInterface
{
    /**
     * @inheritDoc
     */
    public function http($url, $dataLimit = -1, $timeout = -1)
    {
        $this->add([
            'image.http.url'        => $url,
            'image.http.data_limit' => $dataLimit,
            'image.http.timeout'    => $timeout,
        ]);

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function filename($filename)
    {
        $this->set('image.filename', $filename);

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function contents($contents)
    {
        $this->set('image.contents', $contents);

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function resize($width, $height, $option = ImageAwareLayerInterface::RESIZE_SHRINK)
    {
        $this->add([
            'image.resize.width'  => $width,
            'image.resize.height' => $height,
            'image.resize.option' => $option,
        ]);

        return $this;
    }

    /**
     * @inheritDoc
     * @param int $mode one of the IMG_FLIP_ constants
     */
    public function flip($mode)
    {
        $this->add(['image.flip' => $mode]);
        
		return $this;
    }

    /**
     * @inheritDoc
     * @param float $angle angle of rotation
     * @param null|array color of uncovered pixels
     */
    public function rotate($angle, $bgColor = null)
    {
        $this->add(['image.rotate.angle' => $angle]);
        if ($bgColor) {
            $this->add(['image.rotate.bgcolor' => $bgColor]);
        }
        return $this;
    }

	/**
     * @inheritDoc
     * @param int $opacity level of opacity
     */
    public function opacity($opacity)
    {
        $this->add(['image.opacity' => $opacity]);
        
		return $this;
    }
}
