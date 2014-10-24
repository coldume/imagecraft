<?php

namespace Imagecraft\OptionPass;

/**
 * @author Xianghan Wang <coldume@gmail.com>
 * @since  1.0.0
 */
class GifAnimationOptionPass implements OptionPassInterface
{
    /**
     * @inheritDoc
     */
    public function process(array $options)
    {
        if (!isset($options['gif_animation'])) {
            $options['gif_animation'] = true;
        }

        return $options;
    }
}
