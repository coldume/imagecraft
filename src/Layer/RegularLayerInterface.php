<?php

namespace Imagecraft\Layer;

/**
 * @author Xianghan Wang <coldume@gmail.com>
 * @since  1.0.0
 */
interface RegularLayerInterface extends LayerInterface
{
    const MOVE_TOP_LEFT      = 'top_left';
    const MOVE_TOP_CENTER    = 'top_center';
    const MOVE_TOP_RIGHT     = 'top_right';
    const MOVE_CENTER_LEFT   = 'center_left';
    const MOVE_CENTER        = 'center';
    const MOVE_CENTER_RIGHT  = 'center_right';
    const MOVE_BOTTOM_LEFT   = 'bottom_left';
    const MOVE_BOTTOM_CENTER = 'bottom_center';
    const MOVE_BOTTOM_RIGHT  = 'bottom_right';

    /**
     * @param  int    $x
     * @param  int    $y
     * @param  string $gravity
     * @return $this
     * @api
     */
    public function move($x, $y, $gravity = self::MOVE_TOP_LEFT);
}
