<?php

namespace Imagecraft\OptionPass;

/**
 * @author Xianghan Wang <coldume@gmail.com>
 * @since  1.0.0
 */
class OutputFormatOptionPass implements OptionPassInterface
{
    /**
     * @inheritDoc
     */
    public function process(array $options)
    {
        if (!isset($options['output_format'])) {
            $options['output_format'] = 'default';
        }

        return $options;
    }
}
