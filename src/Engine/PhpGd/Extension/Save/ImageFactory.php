<?php

namespace Imagecraft\Engine\PhpGd\Extension\Save;

use Imagecraft\Engine\PhpGd\Helper\ResourceHelper;
use Imagecraft\Image;

/**
 * @author Xianghan Wang <coldume@gmail.com>
 * @since  1.0.0
 */
class ImageFactory
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
     * @param  Imagecraft\Layer\LayerInterface[] $layers
     * @param  mixed[]                             $options
     * @return Image
     */
    public function createImage(array $layers, array $options)
    {
        $this->initContents($layers, $options);
        $contents = $layers[0]->get('final.contents');

        $image = new Image();
        $image->setContents($contents);

        return $image;
    }

    /**
     * @param LayerInterface[] $layers
     * @param mixed[]          $options
     */
    protected function initContents(array $layers, $options)
    {
        $format = $layers[0]->get('image.format');
        if ($layers[0]->has('image.imc_uri')) {
            $uri = $layers[0]->get('image.imc_uri');
            $resource = $this->rh->getGdResourceFromStream($format, $uri, true);
            imagedestroy($resource);
            $contents = file_get_contents($uri);
        } else {
            $contents = $layers[0]->get('image.contents');
            $resource = $this->rh->getGdResourceFromContents($format, $contents, true);
            imagedestroy($resource);
        }

        $layers[0]->set('final.contents', $contents);
    }
}
