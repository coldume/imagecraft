<?php

namespace Imagecraft\Layer;

/**
 * @author Xianghan Wang <coldume@gmail.com>
 * @since  1.0.0
 */
interface TextLayerInterface extends RegularLayerInterface
{
    /**
     * @param  string $filename
     * @param  int    $size
     * @param  string $color
     * @return $this
     * @api
     */
    public function font($filename, $size = 12, $color = '#FFF');

    /**
     * @param  string $label
     * @return $this
     * @api
     */
    public function label($label);

    /**
     * @param  int $angle
     * @return $this
     * @api
     */
    public function angle($angle);

    /**
     * @param  float|int $lineSpacing
     * @return $this
     * @api
     */
    public function lineSpacing($lineSpacing);

    /**
     * @param  int[]       $paddings
     * @param  null|string $color
     * @return $this
     * @api
     */
    public function box(array $paddings, $color = null);
}
