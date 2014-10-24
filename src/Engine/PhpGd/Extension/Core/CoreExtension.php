<?php

namespace Imagecraft\Engine\PhpGd\Extension\Core;

use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Imagecraft\Engine\PhpGd\PhpGdContext;
use Imagecraft\Engine\PhpGd\Helper\ResourceHelper;
use Imagecraft\Engine\PhpGd\Extension\ExtensionInterface;
use Imagecraft\Engine\PhpGd\Extension\Core\EventListener\SystemRequirementListener;
use Imagecraft\Engine\PhpGd\Extension\Core\EventListener\ImageAwareLayerListener;
use Imagecraft\Engine\PhpGd\Extension\Core\EventListener\BackgroundLayerListener;
use Imagecraft\Engine\PhpGd\Extension\Core\EventListener\TextLayerListener;
use Imagecraft\Engine\PhpGd\Extension\Core\EventListener\MemoryRequirementListener;
use Imagecraft\Engine\PhpGd\Extension\Core\EventListener\ImageFactoryListener;
use Imagecraft\Engine\PhpGd\Extension\Core\EventListener\ImageMetadataListener;

/**
 * @author Xianghan Wang <coldume@gmail.com>
 * @since  1.0.0
 */
class CoreExtension implements ExtensionInterface
{
    /**
     * @param EventDispatcherInterface $dispatcher
     */
    public function boot(EventDispatcherInterface $dispatcher)
    {
        $context = new PhpGdContext();
        $rh      = new ResourceHelper();
        $info    = new ImageInfo($context);
        $factory = new ImageFactory($rh);

        $dispatcher->addSubscriber(new SystemRequirementListener($context));
        $dispatcher->addSubscriber(new ImageAwareLayerListener($info, $rh));
        $dispatcher->addSubscriber(new BackgroundLayerListener());
        $dispatcher->addSubscriber(new TextLayerListener($context));
        $dispatcher->addSubscriber(new MemoryRequirementListener($context));
        $dispatcher->addSubscriber(new ImageFactoryListener($factory));
        $dispatcher->addSubscriber(new ImageMetadataListener($context));
    }
}
