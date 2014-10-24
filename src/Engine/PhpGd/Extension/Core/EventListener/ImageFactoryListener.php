<?php

namespace Imagecraft\Engine\PhpGd\Extension\Core\EventListener;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Imagecraft\Engine\PhpGd\PhpGdEvents;
use Imagecraft\Engine\PhpGd\PhpGdEvent;
use Imagecraft\Engine\PhpGd\Extension\Core\ImageFactory;

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
     * @inheritDoc
     */
    public static function getSubscribedEvents()
    {
        return [
            PhpGdEvents::IMAGE => ['createImage', 99],
        ];
    }

    /**
     * @param PhpGdEvent $event
     */
    public function createImage(PhpGdEvent $event)
    {
        $image = $this->factory->createImage($event->getLayers(), $event->getOptions());
        $event->setImage($image);
        $event->stopPropagation();
    }
}
