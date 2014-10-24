<?php

namespace Imagecraft\LayerPass;

use Imagecraft\Layer\LayerInterface;
use Imagecraft\Layer\TextLayerInterface;
use Imagecraft\Exception\BadMethodCallException;

/**
 * @author Xianghan Wang <coldume@gmail.com>
 * @since  1.0.0
 */
class TextLayerPass extends AbstractLayerPass
{
    /**
     * @inheritDoc
     */
    public function process(array $layers)
    {
        foreach ($layers as $key => $layer) {
            if (!($layer instanceof TextLayerInterface)) {
                continue;
            }
            if ('' === $layer->get('text.label')) {
                unset($layers[$key]);
                continue;
            }

            $this->processFont($layer);
            $this->processLabel($layer);
            $this->processAngle($layer);
            $this->processLineSpacing($layer);
            $this->processBox($layer);
        }

        return $layers;
    }

    /**
     * @param  TextLayerInterface $layer
     * @throws BadMethodCallException
     */
    public function processFont(TextLayerInterface $layer)
    {
        if (!$layer->has('text.font.filename')) {
            throw new BadMethodCallException('no.font.added');
        }

        $filename = (string) $layer->get('text.font.filename');
        $layer->set('text.font.filename', $filename);

        $size = (int) $layer->get('text.font.size');
        $size = (5 > $size) ? 5 : $size;
        $layer->set('text.font.size', $size);

        $color = (string) $layer->get('text.font.hex_color');
        $color = $this->sanitizeHexColor($color);
        $layer->set('text.font.hex_color', $color['hex']);
        $layer->set('text.font.rgb_color', $color['rgb']);
    }

    /**
     * @param TextLayerInterface $layer
     */
    public function processLabel(TextLayerInterface $layer)
    {
        if (!$layer->has('text.label')) {
            $label = '';
        } else {
            $label = (string) $layer->get('text.label');
        }
        $layer->set('text.label', $label);
    }

    /**
     * @param TextLayerInterface $layer
     */
    public function processAngle(TextLayerInterface $layer)
    {
        if (!$layer->has('text.angle')) {
            $angle = 0;
        } else {
            $angle = (int) $layer->get('text.angle');
        }
        $layer->set('text.angle', $angle);
    }

    /**
     * @param TextLayerInterface $layer
     */
    public function processLineSpacing(TextLayerInterface $layer)
    {
        if (!$layer->has('text.line_spacing')) {
            $lineSpacing = 0.5;
        } else {
            $lineSpacing = (float) $layer->get('text.line_spacing');
        }
        $layer->set('text.line_spacing', $lineSpacing);
    }

    /**
     * @param TextLayerInterface $layer
     */
    public function processBox(TextLayerInterface $layer)
    {
        if (!$layer->has('text.box.paddings')) {
            $layer->add([
                'text.box.paddings'  => [0, 0, 0, 0],
                'text.box.hex_color' => null,
            ]);

            return;
        }

        $paddings = array_values($layer->get('text.box.paddings'));
        $arr = [];
        for ($i = 0; $i < 4; $i++) {
            if (!isset($paddings[$i]) || 0 > $paddings[$i]) {
                $arr[$i] = 0;
            } else {
                $arr[$i] = (int) $paddings[$i];
            }
        }
        $layer->set('text.box.paddings', $arr);

        if (null !== $color = $layer->get('text.box.hex_color')) {
            $color = $this->sanitizeHexColor($color);
            $layer->set('text.box.hex_color', $color['hex']);
            $layer->set('text.box.rgb_color', $color['rgb']);
        } else {
            $layer->set('text.box.rgb_color', null);
        }
    }
}
