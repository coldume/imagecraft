<?php

namespace Imagecraft\Engine\PhpGd\Extension\Save\EventListener;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Imagecraft\Engine\PhpGd\Extension\Save\ImageFactory;
use Imagecraft\Engine\PhpGd\PhpGdEvents;
use Imagecraft\Engine\PhpGd\PhpGdEvent;

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
            PhpGdEvents::PRE_IMAGE => ['createImage', 839],
        ];
    }

    /**
     * @param PhpGdEvent $event
     */
    public function createImage(PhpGdEvent $event)
    {
        $layers = $event->getLayers();
        if (1 === count($layers) &&
            $layers[0]->get('image.width')  === $layers[0]->get('final.width') &&
            $layers[0]->get('image.height') === $layers[0]->get('final.height') &&
            $layers[0]->get('image.format') === $layers[0]->get('final.format')
        ) {
            $options = $event->getOptions();
            $image   = $this->factory->createImage($layers, $options);
            $event->setImage($image);
            $event->stopPropagation();
        }
    }
}
