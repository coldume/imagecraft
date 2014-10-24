<?php

namespace Imagecraft\LayerPass;

/**
 * @author Xianghan Wang <coldume@gmail.com>
 * @since  1.0.0
 */
class DelegatingLayerPass implements LayerPassInterface
{
    /**
     * @inheritDoc
     */
    public function process(array $layers)
    {
        $passes = $this->getRegisteredPasses();
        foreach ($passes as $pass) {
            $layers = $pass->process($layers);
        }

        return $layers;
    }

    /**
     * return LayerPassInterface[]
     */
    protected function getRegisteredPasses()
    {
        return [
            new BackgroundLayerPass(),
            new RegularLayerPass(),
            new ImageAwareLayerPass(),
            new TextLayerPass(),
        ];
    }
}

