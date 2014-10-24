<?php

namespace Imagecraft\OptionPass;

/**
 * @author Xianghan Wang <coldume@gmail.com>
 * @since  1.0.0
 */
class PngCompressionOptionPass implements OptionPassInterface
{
    /**
     * @inheritDoc
     */
    public function process(array $options)
    {
        if (
            !isset($options['png_compression']) ||
            $options['png_compression'] > 100   ||
            $options['png_compression'] < 0
        ) {
            $options['png_compression'] = 100;
        }

        return $options;
    }
}
