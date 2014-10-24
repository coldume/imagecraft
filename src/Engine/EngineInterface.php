<?php

namespace Imagecraft\Engine;

/**
 * @author Xianghan Wang <coldume@gmail.com>
 * @since  1.0.0
 */
interface EngineInterface
{
    /**
     * @param  \Imagecraft\Layer\LayerInterface[] $layers
     * @param  mixed[]                              $options
     * @return \Imagecraft\Image
     */
    public function getImage(array $layers, array $options);

    /**
     * @param  mixed[] $options
     * @return \Imagecraft\AbstractContext
     */
    public function getContext(array $options);
}
