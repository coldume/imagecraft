<?php

namespace Imagecraft\Engine\PhpGd\Extension\Core;

use Imagecraft\Layer\LayerInterface;
use Imagecraft\Layer\BackgroundLayerInterface;
use Imagecraft\Layer\ImageAwareLayerInterface;
use Imagecraft\Layer\TextLayerInterface;
use Imagecraft\Layer\RegularLayerInterface;
use Imagecraft\Engine\PhpGd\Helper\ResourceHelper;
use Imagecraft\Image;

/**
 * @author Xianghan Wang <coldume@gmail.com>
 * @since  1.0.0
 */
class ImageFactory
{
    /**
     * @var ResourceHelper
     */
    protected $rh;

    /**
     * @param ResourceHelper $rh
     */
    public function __construct(ResourceHelper $rh)
    {
        $this->rh = $rh;
    }

    /**
     * @param  LayerInterface[] $layers
     * @param  mixed[]          $options
     * @return Image
     */
    public function createImage(array $layers, array $options)
    {
        $this->initContents($layers, $options);
        $contents = $layers[0]->get('final.contents');

        $image = new Image();
        $image->setContents($contents);

        return $image;
    }

    /**
     * @param LayerInterface[] $layers
     * @param mixed[]          $options
     */
    protected function initContents(array $layers, $options)
    {
        foreach ($layers as $layer) {
            if ($layer instanceof ImageAwareLayerInterface) {
                $this->initImageAwareLayerResource($layer);
            } elseif ($layer instanceof TextLayerInterface) {
                $this->initTextLayerResource($layer);
            }
            if ($layer instanceof RegularLayerInterface) {
                $resource = $this->rh->getMergedGdResource(
                    $layers[0]->get('final.resource'),
                    $layer->get('final.resource'),
                    $layer->get('regular.move.x'),
                    $layer->get('regular.move.y'),
                    $layer->get('regular.move.gravity'),
                    $layer->get('image.opacity')
                );
                imagedestroy($layer->get('final.resource'));
                $layers[0]->set('final.resource', $resource);
            }
        }
        $contents = $this->rh->getContentsFromGdResource(
            $layers[0]->get('final.format'),
            $layers[0]->get('final.resource'),
            $options,
            true
        );
        imagedestroy($layers[0]->get('final.resource'));

        $layers[0]->set('final.contents', $contents);
    }

    /**
     * @param ImageAwareLayerInterface $layer
     */
    protected function initImageAwareLayerResource(ImageAwareLayerInterface $layer)
    {
        $format = $layer->get('image.format');
        if ($layer->has('image.imc_uri')) {
            $uri = $layer->get('image.imc_uri');
            $resource = $this->rh->getGdResourceFromStream($format, $uri, true);
        } else {
            $contents = $layer->get('image.contents');
            $resource = $this->rh->getGdResourceFromContents($format, $contents, true);
        }
		
		if ($layer->has('image.rotate.angle')) {
            $resource = $this->rh->getRotatedGdResource(
                $resource,
                $layer->get('image.rotate.angle'),
                $layer->get('image.rotate.bgcolor')
            );
        }
		
        if ($layer->has('image.resize.width')) {
            $resource = $this->rh->getResizedGdResource(
                $resource,
                $layer->get('image.resize.width'),
                $layer->get('image.resize.height'),
                $layer->get('image.resize.option'),
                true
            );
        }
        
        if ($layer->has('image.flip')) {
            $resource = $this->rh->getFlippedGdResource($resource, $layer->get('image.flip'));
        }
		
		if ($layer->has('image.opacity')) {
            $resource = $this->rh->getOpacityGdResource($resource, $layer->get('image.opacity'));
        }
		
        $layer->set('final.resource', $resource);
    }

    /**
     * @param TextLayerInterface $layer
     */
    protected function initTextLayerResource(TextLayerInterface $layer)
    {
        $resource = $this->rh->getTextGdResource(
            $layer->get('text.font.filename'),
            $layer->get('text.font.size'),
            $layer->get('text.font.rgb_color'),
            $layer->get('text.label'),
            $layer->get('text.line_spacing'),
            $layer->get('text.angle'),
            $layer->get('text.box.paddings'),
            $layer->get('text.box.rgb_color')
        );

        $layer->set('final.resource', $resource);
    }
}
