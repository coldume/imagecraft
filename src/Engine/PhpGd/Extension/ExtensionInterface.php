<?php

namespace Imagecraft\Engine\PhpGd\Extension;

use Symfony\Component\EventDispatcher\EventDispatcherInterface;

/**
 * @author Xianghan Wang <coldume@gmail.com>
 * @since  1.0.0
 */
interface ExtensionInterface
{
    /**
     * @param EventDispatcherInterface $dispatcher
     */
    public function boot(EventDispatcherInterface $dispatcher);
}
