<?php

namespace Imagecraft\LayerPass;

use Imagecraft\Layer\LayerInterface;
use Imagecraft\Layer\RegularLayerInterface;

/**
 * @author Xianghan Wang <coldume@gmail.com>
 * @since  1.0.0
 */
class RegularLayerPass extends AbstractLayerPass
{
    /**
     * @inheritDoc
     */
    public function process(array $layers)
    {
        foreach ($layers as $layer) {
            if (!($layer instanceof RegularLayerInterface)) {
                continue;
            }

            $this->processMove($layer);
        }

        return $layers;
    }

    /**
     * @param RegularLayerInterface $layer
     */
    public function processMove(RegularLayerInterface $layer)
    {
        if (!$layer->has('regular.move.x')) {
            $layer->add([
                'regular.move.x'       => 0,
                'regular.move.y'       => 0,
                'regular.move.gravity' => RegularLayerInterface::MOVE_CENTER,
            ]);

            return;
        }

        $x = (int) $layer->get('regular.move.x');
        $layer->set('regular.move.x', $x);

        $y = (int) $layer->get('regular.move.y');
        $layer->set('regular.move.y', $y);

        $gravity = (string) $layer->get('regular.move.gravity');
        $gravities = [
            RegularLayerInterface::MOVE_TOP_LEFT,
            RegularLayerInterface::MOVE_TOP_CENTER,
            RegularLayerInterface::MOVE_TOP_RIGHT,
            RegularLayerInterface::MOVE_CENTER_LEFT,
            RegularLayerInterface::MOVE_CENTER,
            RegularLayerInterface::MOVE_CENTER_RIGHT,
            RegularLayerInterface::MOVE_BOTTOM_LEFT,
            RegularLayerInterface::MOVE_BOTTOM_CENTER,
            RegularLayerInterface::MOVE_BOTTOM_RIGHT,
        ];
        $layer->set('regular.move.gravity', $gravity);
    }
}
