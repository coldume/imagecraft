<?php

namespace Imagecraft\OptionPass;

/**
 * @author Xianghan Wang <coldume@gmail.com>
 * @since  1.0.0
 */
class MemoryLimitOptionPass implements OptionPassInterface
{
    /**
     * @inheritDoc
     */
    public function process(array $options)
    {
        if (!isset($options['memory_limit'])) {
            $options['memory_limit'] = -10;
        }

        return $options;
    }
}
