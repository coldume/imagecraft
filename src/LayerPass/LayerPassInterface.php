<?php

namespace Imagecraft\LayerPass;

/**
 * @author Xianghan Wang <coldume@gmail.com>
 * @since  1.0.0
 */
interface LayerPassInterface
{
    /**
     * @param  \Imagecraft\Layer\LayerInterface[] $layers
     * @return \Imagecraft\Layer\LayerInterface[]
     */
    public function process(array $layers);
}
