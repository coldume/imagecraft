<?php

namespace Imagecraft\LayerPass;

use Imagecraft\Layer\LayerInterface;
use Imagecraft\Layer\ImageAwareLayerInterface;
use Imagecraft\Exception\BadMethodCallException;

/**
 * @author Xianghan Wang <coldume@gmail.com>
 * @since  1.0.0
 */
class ImageAwareLayerPass extends AbstractLayerPass
{
    /**
     * @inheritDoc
     */
    public function process(array $layers)
    {
        foreach ($layers as $layer) {
            if (!($layer instanceof ImageAwareLayerInterface)) {
                continue;
            }

            $this->processResource($layer);
            $this->processResize($layer);
        }

        return $layers;
    }

    /**
     * @param  ImageAwareLayerInterface $layer
     * @throws BadMethodCallException
     */
    public function processResource(ImageAwareLayerInterface $layer)
    {
        if ($layer->has('image.http.url')) {
            $url = (string) $layer->get('image.http.url');
            $url = $this->sanitizeUrl($url);
            $layer->set('image.http.url', $url);
            $limit = $layer->get('image.http.data_limit');
            $limit = (0 < $limit) ? (float) $limit : -1;
            $layer->set('image.http.data_limit', $limit);
            $timeout = $layer->get('image.http.timeout');
            $timeout = (0 < $timeout) ? (float) $timeout : -1;
            $layer->set('image.http.timeout', $timeout);
        } elseif ($layer->has('image.filename')) {
            $filename = (string) $layer->get('image.filename');
            $layer->set('image.filename', $filename);
        } elseif ($layer->has('image.contents')) {
            $contents = (string) $layer->get('image.contents');
            $layer->set('image.contents', $contents);
        } else {
            throw new BadMethodCallException('no.image.added');
        }
    }

    /**
     * @param ImageAwareLayerInterface $layer
     */
    public function processResize(ImageAwareLayerInterface $layer)
    {
        if ($layer->has('image.resize.width')) {
            $width  = (int) $layer->get('image.resize.width');
            $width  = (0 >= $width)  ? 1 : $width;
            $layer->set('image.resize.width', $width);

            $height = (int) $layer->get('image.resize.height');
            $height = (0 >= $height) ? 1 : $height;
            $layer->set('image.resize.height', $height);

            $options = [
                ImageAwareLayerInterface::RESIZE_SHRINK,
                ImageAwareLayerInterface::RESIZE_FILL_CROP,
            ];
            $option = (string) $layer->get('image.resize.option');
            $option = $this->sanitizeEnumeration($option, $options);
            $layer->set('image.resize.option', $option);
        }
    }
}
