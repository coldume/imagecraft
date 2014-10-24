<?php

namespace Imagecraft\Layer;

/**
 * @author Xianghan Wang <coldume@gmail.com>
 * @since  1.0.0
 */
class TextLayer extends AbstractLayer implements TextLayerInterface
{
    /**
     * @inheritDoc
     */
    public function font($filename, $size = 12, $color = '#FFF')
    {
        $this->add([
            'text.font.filename'  => $filename,
            'text.font.size'      => $size,
            'text.font.hex_color' => $color,
        ]);

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function label($label)
    {
        $this->set('text.label', $label);

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function angle($angle)
    {
        $this->set('text.angle', $angle);

        return $this;
    }

    public function lineSpacing($lineSpacing)
    {
        $this->set('text.line_spacing', $lineSpacing);

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function box(array $paddings, $color = null)
    {
        $this->add([
            'text.box.paddings'  => $paddings,
            'text.box.hex_color' => $color,
        ]);

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function move($x, $y, $gravity = RegularLayerInterface::MOVE_TOP_LEFT)
    {
        $this->add([
            'regular.move.x'       => $x,
            'regular.move.y'       => $y,
            'regular.move.gravity' => $gravity,
        ]);

        return $this;
    }
}
