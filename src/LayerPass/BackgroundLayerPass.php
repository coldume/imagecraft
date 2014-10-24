<?php

namespace Imagecraft\LayerPass;

use Imagecraft\Exception\BadMethodCallException;

/**
 * @author Xianghan Wang <coldume@gmail.com>
 * @since  1.0.0
 */
class BackgroundLayerPass extends AbstractLayerPass
{
    /**
     * @inheritDoc
     */
    public function process(array $layers)
    {
        if (!isset($layers[0])) {
            throw new BadMethodCallException('no.background.layer.added');
        }

        return $layers;
    }
}
