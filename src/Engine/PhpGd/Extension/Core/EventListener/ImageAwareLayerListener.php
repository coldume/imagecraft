<?php

namespace Imagecraft\Engine\PhpGd\Extension\Core\EventListener;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use ImcStream\ImcStream;
use Imagecraft\Layer\ImageAwareLayerInterface;
use Imagecraft\Engine\PhpGd\PhpGdEvents;
use Imagecraft\Engine\PhpGd\PhpGdEvent;
use Imagecraft\Engine\PhpGd\Helper\ResourceHelper;
use Imagecraft\Engine\PhpGd\Extension\Core\ImageInfo;

/**
 * @author Xianghan Wang <coldume@gmail.com>
 * @since  1.0.0
 */
class ImageAwareLayerListener implements EventSubscriberInterface
{
    /**
     * @var ImageInfo
     */
    protected $info;

    /**
     * @var ResourceHelper
     */
    protected $rh;

    /**
     * @param ImageInfo
     */
    public function __construct(ImageInfo $info, ResourceHelper $rh)
    {
        $this->info = $info;
        $this->rh   = $rh;
    }

    /**
     * @inheritDoc
     */
    public static function getSubscribedEvents()
    {
        return [
            PhpGdEvents::PRE_IMAGE => [
                ['initImcUri', 909],
                ['initFilePointer', 899],
                ['initImageInfo', 889],
                ['initFinalDimensions', 879],
                ['termFilePointer', 99],
            ],
            PhpGdEvents::FINISH_IMAGE => [
                ['termFilePointer', 99],
                ['termImcUri', 89],
            ]
        ];
    }

    /**
     * @param PhpGdEvent
     */
    public function initImcUri(PhpGdEvent $event)
    {
        ImcStream::register();
        $layers = $event->getLayers();
        foreach ($layers as $key => $layer) {
            if (!($layer instanceof ImageAwareLayerInterface)) {
                continue;
            }
            $arr = false;
            if ($layer->has('image.http.url')) {
                $arr = [
                    'uri'        => $layer->get('image.http.url'),
                    'data_limit' => $layer->get('image.http.data_limit'),
                    'timeout'    => $layer->get('image.http.timeout'),
                    'seek'       => true,
                    'global'     => true,
                ];
            } elseif ($layer->has('image.filename')) {
                $arr = [
                    'uri'  => $layer->get('image.filename'),
                    'seek' => true
                ];
            }
            if ($arr) {
                $uri = 'imc://'.serialize($arr);
                $layer->set('image.imc_uri', $uri);
            }
        }
    }

    /**
     * @param PhpGdEvent
     */
    public function initFilePointer(PhpGdEvent $event)
    {
        $layers = $event->getLayers();
        foreach ($layers as $key => $layer) {
            if (!($layer instanceof ImageAwareLayerInterface)) {
                continue;
            }
            if ($layer->has('image.imc_uri')) {
                $fp = fopen($layer->get('image.imc_uri'), 'rb');
            } elseif ($layer->has('image.contents')) {
                $fp = fopen('php://temp', 'rb+');
                fwrite($fp, $layer->get('image.contents'));
            }
            $layer->set('image.fp', $fp);
        }
    }

    /**
     * @param PhpGdEvent
     */
    public function initImageInfo(PhpGdEvent $event)
    {
        $layers = $event->getLayers();
        foreach ($layers as $layer) {
            if (!($layer instanceof ImageAwareLayerInterface)) {
                continue;
            }
            $info = $this->info->resolveFromFilePointer($layer->get('image.fp'));
            $layer->add([
                'image.width'  => $info['width'],
                'image.height' => $info['height'],
                'image.format' => $info['format'],
            ]);
        }
    }

    /**
     * @param PhpGdEvent
     */
    public function initFinalDimensions(PhpGdEvent $event)
    {
        $layers = $event->getLayers();
        foreach ($layers as $layer) {
            if (!($layer instanceof ImageAwareLayerInterface)) {
                continue;
            }
            $width  = $layer->get('image.width');
            $height = $layer->get('image.height');
            if ($layer->has('image.resize.width')) {
                $args = $this->rh->getResizeArguments(
                    $width,
                    $height,
                    $layer->get('image.resize.width'),
                    $layer->get('image.resize.height'),
                    $layer->get('image.resize.option')
                );
                if ($args) {
                    $width  = $args['dst_w'];
                    $height = $args['dst_h'];
                }
            }
            $layer->add(['final.width' => $width, 'final.height' => $height]);
        }
    }

    /**
     * @param PhpGdEvent
     */
    public function termFilePointer(PhpGdEvent $event)
    {
        $layers = $event->getLayers();
        foreach ($layers as $layer) {
            if (!($layer instanceof ImageAwareLayerInterface)) {
                continue;
            }
            if ($layer->has('image.fp')) {
                fclose($layer->get('image.fp'));
                $layer->remove('image.fp');
            }
        }
    }

    /**
     * @param PhpGdEvent
     */
    public function termImcUri(PhpGdEvent $event)
    {
        $layers = $event->getLayers();
        foreach ($layers as $key => $layer) {
            if (!($layer instanceof ImageAwareLayerInterface)) {
                continue;
            }
            if ($layer->has('image.imc_uri')) {
                ImcStream::fclose($layer->get('image.imc_uri'));
                $layer->remove('image.imc_uri');
            }
        }
    }

}
