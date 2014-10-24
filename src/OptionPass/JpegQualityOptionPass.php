<?php

namespace Imagecraft\OptionPass;

/**
 * @author Xianghan Wang <coldume@gmail.com>
 * @since  1.0.0
 */
class JpegQualityOptionPass implements OptionPassInterface
{
    /**
     * @inheritDoc
     */
    public function process(array $options)
    {
        if (
            !isset($options['jpeg_quality']) ||
            $options['jpeg_quality'] > 100   ||
            $options['jpeg_quality'] < 0
        ) {
            $options['jpeg_quality'] = 100;
        }

        return $options;
    }
}
