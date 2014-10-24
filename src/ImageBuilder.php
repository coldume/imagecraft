<?php

namespace Imagecraft;

use TranslatedException\TranslatedException;
use Imagecraft\Layer\BackgroundLayer;
use Imagecraft\Layer\ImageLayer;
use Imagecraft\Layer\TextLayer;
use Imagecraft\LayerPass\DelegatingLayerPass;
use Imagecraft\OptionPass\DelegatingOptionPass;
use Imagecraft\Engine\DelegatingEngine;

/**
 * @author Xianghan Wang <coldume@gmail.com>
 * @since  1.0.0
 */
class ImageBuilder
{
    /**
     * @var mixed[]
     */
    protected $options;

    /**
     * @var \Imagecraft\Layer\LayerInterface[]
     */
    protected $layers;

    /**
     * @param mixed[] $options
     */
    public function __construct(array $options = [])
    {
        $this->options = $options;
        $this->layers  = [0 => null];
    }

    /**
     * @return BackgroundLayer
     * @api
     */
    public function addBackgroundLayer()
    {
        return $this->layers[0] = new BackgroundLayer($this);
    }

    /**
     * @return ImageLayer
     * @api
     */
    public function addImageLayer()
    {
        return $this->layers[] = new ImageLayer($this);
    }

    /**
     * @return TextLayer
     * @api
     */
    public function addTextLayer()
    {
        return $this->layers[] = new TextLayer($this);
    }

    /**
     * @return Image
     * @api
     */
    public function save()
    {
        try {
            $pass = new DelegatingOptionPass();
            $this->options = $pass->process($this->options);
            TranslatedException::init($this->options);
            TranslatedException::addResourceDir(__DIR__.'/Resources/translations');
            $pass = new DelegatingLayerPass();
            $this->layers = $pass->process($this->layers);
            $engine = new DelegatingEngine();
            $image  = $engine->getImage($this->layers, $this->options);
        } catch (TranslatedException $e) {
            $image = new Image();
            $image->setMessage($e->getMessage());
            $image->setVerboseMessage($e->getVerboseMessage());
        }
        $this->layers = [0 => null];

        return $image;
    }

    /**
     * @return AbstractContext
     * @api
     */
    public function about()
    {
        $engine  = new DelegatingEngine($options['engine']);
        $context = $engine->getContext($this->options);

        return $context;
    }
}
