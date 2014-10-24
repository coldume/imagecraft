<?php

namespace Imagecraft\Engine\PhpGd\Extension\Gif;

use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Imagecraft\Engine\PhpGd\PhpGdContext;
use Imagecraft\Engine\PhpGd\Helper\ResourceHelper;
use Imagecraft\Engine\PhpGd\Extension\ExtensionInterface;
use Imagecraft\Engine\PhpGd\Extension\Gif\EventListener\GifExtractorListener;
use Imagecraft\Engine\PhpGd\Extension\Gif\EventListener\MemoryRequirementListener;
use Imagecraft\Engine\PhpGd\Extension\Gif\EventListener\ImageFactoryListener;

/**
 * @author Xianghan Wang <coldume@gmail.com>
 * @since  1.0.0
 */
class GifExtension implements ExtensionInterface
{
    /**
     * @param EventDispatcherInterface $dispatcher
     */
    public function boot(EventDispatcherInterface $dispatcher)
    {
        $context     = new PhpGdContext();
        $rh          = new ResourceHelper();
        $extractor   = new GifExtractor();
        $builder     = new GifBuilder();
        $builderPlus = new GifBuilderPlus();
        $optimizer   = new GifOptimizer($rh);
        $factory     = new ImageFactory($rh, $extractor, $builder, $builderPlus, $optimizer);

        $dispatcher->addSubscriber(new GifExtractorListener($extractor));
        $dispatcher->addSubscriber(new MemoryRequirementListener($context));
        $dispatcher->addSubscriber(new ImageFactoryListener($factory));
    }
}
