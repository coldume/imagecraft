<?php

namespace Imagecraft\Engine\PhpGd;

use Symfony\Component\EventDispatcher\EventDispatcher;
use Imagecraft\Engine\EngineInterface;
use Imagecraft\Engine\PhpGd\Extension\DelegatingExtension;

/**
 * @author Xianghan Wang <coldume@gmail.com>
 * @since  1.0.0
 */
class PhpGdEngine implements EngineInterface
{
    /**
     * @inheritDoc
     */
    public function getImage(array $layers, array $options)
    {
        $dispatcher = new EventDispatcher();
        $extension  = new DelegatingExtension();
        $extension->boot($dispatcher);

        $event = new PhpGdEvent($layers, $options);
        $dispatcher->dispatch(PhpGdEvents::PRE_IMAGE, $event);

        if (!$event->getImage()) {
            $event = new PhpGdEvent($layers, $options);
            $dispatcher->dispatch(PhpGdEvents::IMAGE, $event);
        }

        $image = $event->getImage();
        $event = new PhpGdEvent($layers, $options);
        $event->setImage($image);
        $dispatcher->dispatch(PhpGdEvents::FINISH_IMAGE, $event);

        return $event->getImage();
    }

    /**
     * @inheritDoc
     */
    public function getContext(array $options)
    {
        return new PhpGdContext();
    }
}
