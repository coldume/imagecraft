<?php

namespace Imagecraft\Engine\PhpGd\Extension\Gif\EventListener;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Imagecraft\Exception\TranslatedException;
use Imagecraft\Engine\PhpGd\PhpGdEvents;
use Imagecraft\Engine\PhpGd\PhpGdEvent;
use Imagecraft\Engine\PhpGd\Extension\Gif\ImageFactory;

/**
 * @author Xianghan Wang <coldume@gmail.com>
 * @since  1.0.0
 */
class ImageFactoryListener implements EventSubscriberInterface
{
    /**
     * @var ImageFactory
     */
    protected $factory;

    /**
     * @var mixed[]
     */
    protected $extras = [];

    /**
     * @param ImageFactory $factory
     */
    public function __construct(ImageFactory $factory)
    {
        $this->factory = $factory;
    }

    /**
     * @return mixed[]
     */
    public static function getSubscribedEvents()
    {
        return [
            PhpGdEvents::IMAGE        => ['createImage', 199],
            PhpGdEvents::FINISH_IMAGE => ['addImageExtras', 869],
        ];
    }

    /**
     * @param PhpGdEvent $event
     */
    public function createImage(PhpGdEvent $event)
    {
        $layers = $event->getLayers();
        if (!$layers[0]->has('gif.extracted')) {
            return;
        }

        try {
            $options = $event->getOptions();
            $image   = $this->factory->createImage($layers, $options);
            $event->setImage($image);
            $event->stopPropagation();
        } catch (\Exception $e) {
            $e = new TranslatedException('gif.animation.may.lost.due.to.corrupted.frame.data');
            $this->extras['gif_error'] = $e->getMessage();
        }
    }

    /**
     * param PhpGdEvent $event
     */
    public function addImageExtras(PhpGdEvent $event)
    {
        if (!$this->extras) {
            return;
        }
        $image = $event->getImage();
        $image->addExtras($this->extras);
    }
}
