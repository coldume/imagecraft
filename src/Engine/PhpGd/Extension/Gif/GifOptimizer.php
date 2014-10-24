<?php

namespace Imagecraft\Engine\PhpGd\Extension\Gif;

use Imagecraft\Engine\PhpGd\Helper\ResourceHelper;

/**
 * @author Xianghan Wang <coldume@gmail.com>
 * @since  1.0.0
 */
class GifOptimizer
{
    /**
     * @var ResourceHelper
     */
    protected $rh;

    /**
     * @param ResourceHelper $rh
     */
    public function __construct(ResourceHelper $rh)
    {
        $this->rh = $rh;
    }

    /**
     * @param  resource $resource
     * @param  resource $controlResource
     * @return resource
     */
    public function getOptimizedGdResource($resource, $controlResource)
    {
        if (imageistruecolor($resource)) {
            $resource = $this->rh->getPalettizedGdResource($resource);
        }
        $totalColors = imagecolorstotal($resource);
        $trans       = imagecolortransparent($resource);
        $reds        = new \SplFixedArray($totalColors);
        $greens      = new \SplFixedArray($totalColors);
        $blues       = new \SplFixedArray($totalColors);

        $i = 0;
        do {
            $colors = imagecolorsforindex($resource, $i);
            $reds[$i]   = $colors['red'];
            $greens[$i] = $colors['green'];
            $blues[$i]  = $colors['blue'];
        } while (++$i < $totalColors);

        if (imageistruecolor($controlResource)) {
            $controlResource = $this->rh->getPalettizedGdResource($controlResource);
        }
        $controlTotalColors = imagecolorstotal($controlResource);
        $controlTrans       = imagecolortransparent($controlResource);
        $controlReds        = new \SplFixedArray($controlTotalColors);
        $controlGreens      = new \SplFixedArray($controlTotalColors);
        $controlBlues       = new \SplFixedArray($controlTotalColors);
        $i = 0;
        do {
            $colors = imagecolorsforindex($controlResource, $i);
            $controlReds[$i]   = $colors['red'];
            $controlGreens[$i] = $colors['green'];
            $controlBlues[$i]  = $colors['blue'];
        } while (++$i < $controlTotalColors);

        $width  = imagesx($resource);
        $height = imagesy($resource);
        $y = 0;
        do{
            $x = 0;
            do {
                $index        = imagecolorat($resource, $x, $y);
                $red          = $reds[$index];
                $green        = $greens[$index];
                $blue         = $blues[$index];
                $controlIndex = imagecolorat($controlResource, $x, $y);
                $controlRed   = $controlReds[$controlIndex];
                $controlGreen = $controlGreens[$controlIndex];
                $controlBlue  = $controlBlues[$controlIndex];
                if (
                    (($red   & 0b11111100) === ($controlRed   & 0b11111100)) &&
                    (($green & 0b11111100) === ($controlGreen & 0b11111100)) &&
                    (($blue  & 0b11111100) === ($controlBlue  & 0b11111100))
                ) {
                    imagesetpixel($resource, $x, $y, $trans);
                }
            } while (++$x !== $width);
        } while (++$y !== $height);

        return $resource;
    }
}
