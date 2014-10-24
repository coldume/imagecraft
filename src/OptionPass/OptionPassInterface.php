<?php

namespace Imagecraft\OptionPass;

/**
 * @author Xianghan Wang <coldume@gmail.com>
 * @since  1.0.0
 */
interface OptionPassInterface
{
    /**
     * @param  mixed[] $options
     * @return mixed[]
     */
    public function process(array $options);
}
