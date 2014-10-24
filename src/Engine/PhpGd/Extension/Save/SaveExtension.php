<?php

namespace Imagecraft\Engine\PhpGd\Extension\Save;

use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Imagecraft\Engine\PhpGd\Helper\ResourceHelper;
use Imagecraft\Engine\PhpGd\Extension\ExtensionInterface;
use Imagecraft\Engine\PhpGd\Extension\Save\EventListener\ImageFactoryListener;

/**
 * @author Xianghan Wang <coldume@gmail.com>
 * @since  1.0.0
 */
class SaveExtension implements ExtensionInterface
{
    /**
     * @param EventDispatcherInterface $dispatcher
     */
    public function boot(EventDispatcherInterface $dispatcher)
    {
        $rh      = new ResourceHelper();
        $factory = new ImageFactory($rh);

        $dispatcher->addSubscriber(new ImageFactoryListener($factory));
    }
}
