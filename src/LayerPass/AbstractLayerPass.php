<?php

namespace Imagecraft\LayerPass;

use Imagecraft\Exception\InvalidArgumentException;

/**
 * @author Xianghan Wang <coldume@gmail.com>
 * @since  1.0.0
 */
abstract class AbstractLayerPass implements LayerPassInterface
{
    /**
     * @param  string $color
     * @return mixed[]
     * @throws InvalidArgumentException
     */
    public function sanitizeHexColor($color)
    {
        if (!preg_match('/\\A#?([[:xdigit:]]{3}|[[:xdigit:]]{6})\\Z/', $color, $matches)) {
            throw new InvalidArgumentException(
                'invalid.hex.color.%cp_invalid%.%example%',
                ['%cp_invalid%' => '"'.$color.'"', '%example%' => '"#CCC", "#F9F9F9"']
            );
        }

        $hex = strtoupper($matches[1]);
        if (3 === strlen($hex)) {
            $red   = hexdec(substr($hex, 0, 1) . substr($hex, 0, 1));
            $green = hexdec(substr($hex, 1, 1) . substr($hex, 1, 1));
            $blue  = hexdec(substr($hex, 2, 1) . substr($hex, 2, 1));
        } else {
            $red   = hexdec(substr($hex, 0, 2));
            $green = hexdec(substr($hex, 2, 2));
            $blue  = hexdec(substr($hex, 4, 2));
        }

        return ['hex' => '#'.$hex, 'rgb' => [$red, $green, $blue]];
    }

    /**
     * @param  mixed   $element
     * @param  mixed[] $array
     * @return mixed
     * @throws InvalidArgumentException
     */
    public function sanitizeEnumeration($element, array $array)
    {
        if (!in_array($element, $array)) {
            throw new InvalidArgumentException(
                'unexpected.argument.%cp_unexpected%.%expected%',
                [
                    '%cp_unexpected%' => '"'.$element.'"',
                    '%expected%'      => implode(', ', preg_replace('/.+/', '"$0"', $array)),
                ]
            );
        }

        return $element;
    }

    /**
     * @param  string $url
     * @return string
     */
    public function sanitizeUrl($url)
    {
        if (0 === preg_match("#https?://#", $url)) {
            $url = 'http://'.$url;
        }

        return $url;
    }
}
