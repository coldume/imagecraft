<?php

namespace Imagecraft\Engine\PhpGd\Extension\Gif;

use Imagecraft\Image;
use Imagecraft\Layer\LayerInterface;
use Imagecraft\Layer\BackgroundLayerInterface;
use Imagecraft\Layer\RegularLayerInterface;
use Imagecraft\Layer\ImageAwareLayerInterface;
use Imagecraft\Layer\TextLayerInterface;
use Imagecraft\Engine\PhpGd\PhpGdContext;
use Imagecraft\Engine\PhpGd\Helper\ResourceHelper;

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
     * @var GifExtractor
     */
    protected $extractor;

    /**
     * @var GifBuilder
     */
    protected $builder;

    /**
     * @var GifBuilderPlus
     */
    protected $builderPlus;

    /**
     * @var GifOptimizer
     */
    protected $optimizer;

    /**
     * @var null|resource
     */
    protected $lp;

    /**
     * @var null|resource
     */
    protected $cp;

    /**
     * @param ResourceHelper $rh
     * @param GifExtractor   $extractor
     * @param GifBuilder     $builder
     * @param GifBuilderPlus $builderPlus
     * @param GifOptimizer   $optimizer
     */
    public function __construct(
        ResourceHelper $rh,
        GifExtractor $extractor,
        GifBuilder $builder,
        GifBuilderPlus $builderPlus,
        GifOptimizer $optimizer
    ) {
        $this->rh          = $rh;
        $this->extractor   = $extractor;
        $this->builder     = $builder;
        $this->builderPlus = $builderPlus;
        $this->optimizer   = $optimizer;
    }

    /**
     * @param  LayerInterface[] $layers
     * @param  mixed[]          $options
     * @return Image
     */
    public function createImage(array $layers, array $options)
    {
        $this->initContents($layers);
        $contents  = $layers[0]->get('gif.contents');

        $extracted = $layers[0]->get('gif.extracted');
        $frames    = count($extracted);

        $image = new Image();
        $image->setContents($contents);
        $image->addExtras(['total_frames' => $frames]);

        return $image;
    }

    /**
     * @param LayerInterface[] $layers
     */
    protected function initContents(array $layers)
    {
        $this->initQuality($layers[0]);
        $this->initCacheResource($layers);

        $extracted = $layers[0]->get('gif.extracted');
        $this->builderPlus
            ->canvasWidth($layers[0]->get('final.width'))
            ->canvasHeight($layers[0]->get('final.height'))
            ->loop($extracted->getTotalLoops())
        ;
        for ($i = 0, $extracted->seek(0); $extracted->valid(); $extracted->seek(++$i)) {
            $this->initFrameResource($layers[0]);
            $this->coalesceFrameResource($layers[0]);
            $this->renderFrameResource($layers[0]);
            $this->mergeFrameResource($layers[0]);
            $this->initFrameContents($layers[0]);
            $contents = $layers[0]->get('gif.frame_contents');
            $tmpExtracted = $this->extractor->extractFromContents($contents);
            $this->builderPlus->addFrame();
            $this->builderPlus
                ->imageWidth($tmpExtracted->getImageWidth())
                ->imageHeight($tmpExtracted->getImageHeight())
                ->imageLeft($tmpExtracted->getImageLeft())
                ->imageTop($tmpExtracted->getImageTop())
                ->dispose($extracted->getDisposalMethod())
                ->delayTime($extracted->getDelayTime())
                ->interlaceFlag($tmpExtracted->getInterlaceFlag())
                ->colorTable($tmpExtracted->getColorTable())
                ->imageData($tmpExtracted->getImageData())
            ;
            if ($tmpExtracted->getTransparentColorFlag()) {
                $index = $tmpExtracted->getTransparentColorIndex();
                $this->builderPlus->transparentColorIndex($index);
            }
        }
        $contents = $this->builderPlus->getContents();

        $layers[0]->set('gif.contents', $contents);
    }

    /**
     * @param BackgroundLayerInterface $layer
     */
    protected function initQuality(BackgroundLayerInterface $layer)
    {
        $extracted = $layer->get('gif.extracted');
        $width     = $layer->get('final.width');
        $height    = $layer->get('final.height');

        $frames = count($extracted);
        $pixels = $frames * $width * $height;
        if ((250000 > $width * $height) && 2400000 > $pixels) {
            $quality = true;
        } else {
            $quality = false;
        }

        $layer->set('gif.quality', $quality);
    }

    /**
     * @param LayerInterface[] $layers
     */
    protected function initCacheResource(array $layers)
    {
        if (1 == count($layers)) {
            return;
        }
        foreach ($layers as $layer) {
            if ($layer instanceof BackgroundLayerInterface) {
                $width  = $layer->get('final.width');
                $height = $layer->get('final.height');
                $resource = $this->rh->getEmptyGdResource($width, $height);
                $layer->set('gif.cache_resource', $resource);
                $backgroundLayer = $layer;
                continue;
            }
            if ($layer instanceof ImageAwareLayerInterface) {
                $this->initImageAwareLayerResource($layer);
            } elseif ($layer instanceof TextLayerInterface) {
                $this->initTextLayerResource($layer);
            }
            $resource = $this->rh->getMergedGdResource(
                $backgroundLayer->get('gif.cache_resource'),
                $layer->get('final.resource'),
                $layer->get('regular.move.x'),
                $layer->get('regular.move.y'),
                $layer->get('regular.move.gravity')
            );
            if (!$backgroundLayer->get('gif.quality') && imageistruecolor($resource)) {
                $resource = $this->rh->getPalettizedGdResource($resource);
            }
            imagedestroy($layer->get('final.resource'));

            $backgroundLayer->set('gif.cache_resource', $resource);
        }
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

        if ($layer->has('image.resize.width')) {
            $resource = $this->rh->getResizedGdResource(
                $resource,
                $layer->get('image.resize.width'),
                $layer->get('image.resize.height'),
                $layer->get('image.resize.option'),
                true
            );
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

    /**
     * @param BackgroundLayerInterface $layer
     */
    protected function initFrameResource(BackgroundLayerInterface $layer)
    {
        $extracted = $layer->get('gif.extracted');
        $this->builder
            ->imageWidth($extracted->getImageWidth())
            ->imageHeight($extracted->getImageHeight())
            ->colorTable($extracted->getColorTable())
            ->interlaceFlag($extracted->getInterlaceFlag())
            ->imageData($extracted->getImageData())
        ;
        if ($extracted->getTransparentColorFlag()) {
            $index = $extracted->getTransparentColorIndex();
            $this->builder->transparentColorIndex($index);
        }
        $contents = $this->builder->getContents();
        $resource = $this->getGifResourceFromContents($contents);

        $layer->set('gif.frame_resource', $resource);
    }

    /**
     * @param BackgroundLayerInterface $layer
     */
    protected function coalesceFrameResource(BackgroundLayerInterface $layer)
    {
        $extracted = $layer->get('gif.extracted');
        $quality   = $layer->get('gif.quality');
        $resource  = $layer->get('gif.frame_resource');
        $dm        = $extracted->getDisposalMethod();
        $lm        = $extracted->getLinkedDisposalMethod();
        $dstX      = $extracted->getImageLeft();
        $dstY      = $extracted->getImageTop();
        $width     = $extracted->getCanvasWidth();
        $height    = $extracted->getCanvasHeight();

        if ($quality) {
            if (GifExtracted::DISPOSAL_METHOD_NONE !== $lm) {
                $newResource = $this->rh->getEmptyGdResource($width, $height);
            } else {
                $newResource = $this->lp;
                if (GifExtracted::DISPOSAL_METHOD_PREVIOUS === $dm) {
                    $newResource = $this->rh->getClonedGdResource($newResource);
                }
            }
            $newResource = $this->rh->getMergedGdResource($newResource, $resource, $dstX, $dstY);
            imagedestroy($resource);
            if (GifExtracted::DISPOSAL_METHOD_NONE === $dm) {
                $this->lp = $newResource;
                $newResource = $this->rh->getClonedGdResource($newResource);
            }
        } else {
            $newResource = $this->rh->getEmptyGdResource($width, $height);
            $newResource = $this->rh->getMergedGdResource($newResource, $resource, $dstX, $dstY);
            imagedestroy($resource);
        }

        $layer->set('gif.frame_resource', $newResource);
    }

    /**
     * @param BackgroundLayerInterface $layer
     */
    protected function renderFrameResource(BackgroundLayerInterface $layer)
    {
        if ($layer->has('image.resize.width')) {
            $resource = $this->rh->getResizedGdResource(
                $layer->get('gif.frame_resource'),
                $layer->get('image.resize.width'),
                $layer->get('image.resize.height'),
                $layer->get('image.resize.option'),
                $layer->get('gif.quality')
            );
            $layer->set('gif.frame_resource', $resource);
        }
    }

    /**
     * @param BackgroundLayerInterface $layer
     */
    protected function mergeFrameResource(BackgroundLayerInterface $layer)
    {
        if (!$layer->has('gif.cache_resource')) {
            return;
        }
        $resource      = $layer->get('gif.frame_resource');
        $cacheResource = $layer->get('gif.cache_resource');
        $extracted     = $layer->get('gif.extracted');

        $resource = $this->rh->getMergedGdResource($resource, $cacheResource);
        if ($extracted->last()) {
            imagedestroy($cacheResource);
        }

        $layer->set('gif.frame_resource', $resource);
    }

    /**
     * @param BackgroundLayerInterface $layer
     */
    protected function initFrameContents(BackgroundLayerInterface $layer)
    {
        $extracted = $layer->get('gif.extracted');
        $quality   = $layer->get('gif.quality');
        $resource  = $layer->get('gif.frame_resource');
        $dm        = $extracted->getDisposalMethod();
        $lm        = $extracted->getLinkedDisposalMethod();

        if (!$quality) {
            $contents = $this->getGifContentsFromGdResource($resource);
            imagedestroy($resource);
            $layer->set('gif.frame_contents', $contents);

            return;
        }

        if (GifExtracted::DISPOSAL_METHOD_NONE === $lm) {
            $controlResource = $layer->get('gif.control_resource');
            if (GifExtracted::DISPOSAL_METHOD_NONE === $dm && !$extracted->last()) {
                $clonedResource = $this->rh->getClonedGdResource($resource);
                $layer->set('gif.control_resource', $clonedResource);
            }
            $resource = $this->optimizer->getOptimizedGdResource($resource, $controlResource);
            if (GifExtracted::DISPOSAL_METHOD_PREVIOUS !== $dm) {
                imagedestroy($controlResource);
            }
            $contents = $this->getGifContentsFromGdResource($resource);
            imagedestroy($resource);
        } else {
            $contents = $this->getGifContentsFromGdResource($resource);
            if (GifExtracted::DISPOSAL_METHOD_NONE === $dm && !$extracted->last()) {
                $layer->set('gif.control_resource', $resource);
            } else {
                imagedestroy($resource);
            }
        }
        $layer->set('gif.frame_contents', $contents);

    }

    /**
     * @param  resource $resource
     * @return string
     */
    protected function getGifContentsFromGdResource($resource)
    {
        return $this->rh->getContentsFromGdResource(PhpGdContext::FORMAT_GIF, $resource, [], true);
    }

    /**
     * @param  string $contents
     * @return resource
     */
    protected function getGifResourceFromContents($contents)
    {
        return $this->rh->getGdResourceFromContents(PhpGdContext::FORMAT_GIF, $contents, true);
    }
}
