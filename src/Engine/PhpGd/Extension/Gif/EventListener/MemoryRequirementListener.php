<?php

namespace Imagecraft\Engine\PhpGd\Extension\Gif\EventListener;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Imagecraft\Exception\TranslatedException;
use Imagecraft\Engine\PhpGd\PhpGdContext;
use Imagecraft\Engine\PhpGd\PhpGdEvents;
use Imagecraft\Engine\PhpGd\PhpGdEvent;

/**
 * @author Xianghan Wang <coldume@gmail.com>
 * @since  1.0.0
 */
class MemoryRequirementListener implements EventSubscriberInterface
{
    /**
     * @var PhpGdContext
     */
    protected $context;

    /**
     * @var mixed[]
     */
    protected $extras = [];

    /**
     * @param PhpGdContext $context
     */
    public function __construct(PhpGdContext $context)
    {
        $this->context = $context;
    }

    /**
     * @return mixed[]
     */
    public static function getSubscribedEvents()
    {
        return [
            PhpGdEvents::PRE_IMAGE    => ['verifyMemoryLimit', 819],
            PhpGdEvents::FINISH_IMAGE => ['addImageExtras', 199],
        ];
    }

    /**
     * @param PhpGdEvent $event
     */
    public function verifyMemoryLimit(PhpGdEvent $event)
    {
        $layers = $event->getLayers();
        $options = $event->getOptions();
        if (!$layers[0]->has('gif.extracted')) {
            return;
        }

        $totalFrames = count($layers[0]->get('gif.extracted'));
        $width       = $layers[0]->get('image.width');
        $height      = $layers[0]->get('image.height');
        $pixels      = $width * $height;
        $finalWidth  = $layers[0]->get('final.width');
        $finalHeight = $layers[0]->get('final.height');
        $finalPixels = $finalWidth * $finalHeight;
        $totalPixels = $totalFrames * $finalPixels;
        $limit       = $this->context->getMemoryLimit($options['memory_limit']);
        $peak        = memory_get_peak_usage(true) + 15 * 1024 * 1024 + 3 * ($pixels + $finalPixels);

        if (1000000 < $finalPixels || 57000000 < $totalPixels || $peak > $limit) {
            $e = new TranslatedException(
                'gif.animation.may.lost.as.too.many.frames.or.dimensions.too.large.%total_frames%.%dimensions%',
                ['%total_frames%' => $totalFrames, '%dimensions%' => $width.'x'.$height]
            );
            $this->extras['gif_error'] = $e->getMessage();
            $layers[0]->remove('gif.extracted');
        } else {
            $this->extras['memory_approx'] = number_format($peak / (1024 * 1024), 2).' MB';
        }
    }

    /**
     * param PhpGdEvent $event
     */
    public function addImageExtras(PhpGdEvent $event)
    {
        $image = $event->getImage();
        if (array_key_exists('gif_error', $image->getExtras())) {
            return;
        }
        $image->addExtras($this->extras);
    }
}
