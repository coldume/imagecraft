<?php

namespace Imagecraft\Layer;

/**
 * @author Xianghan Wang <coldume@gmail.com>
 * @since  1.0.0
 */
interface LayerInterface extends ParameterBagInterface
{
    /**
     * @return null|\Imagecraft\ImageBuilderInterface
     * @api
     */
    public function done();

}
