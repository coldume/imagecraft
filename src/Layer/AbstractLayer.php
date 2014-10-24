<?php

namespace Imagecraft\Layer;

use Imagecraft\ImageBuilder;

/**
 * @author Xianghan Wang <coldume@gmail.com>
 * @since  1.0.0
 */
abstract class AbstractLayer extends ParameterBag implements LayerInterface
{
    /**
     * @param null|ImageBuilder $builder
     */
    public function __construct(ImageBuilder $builder = null)
    {
        $this->set('image_builder', $builder);
    }

    /**
     * @inheritDoc
     */
    public function done()
    {
        return $this->get('image_builder');
    }
}
