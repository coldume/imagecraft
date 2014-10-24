<?php

namespace Imagecraft\Layer;

/**
 * @author Xianghan Wang <coldume@gmail.com>
 * @since  1.0.0
 */
class ParameterBag implements ParameterBagInterface
{
    /**
     * @inheritDoc
     */
    protected $parameters = [];

    /**
     * @inheritDoc
     */
    public function set($name, $value)
    {
        $this->parameters[$name] = $value;
    }

    /**
     * @inheritDoc
     */
    public function add(array $parameters)
    {
        $this->parameters = array_replace($this->parameters, $parameters);
    }

    /**
     * @inheritDoc
     */
    public function get($name)
    {
        return $this->has($name) ? $this->parameters[$name] : null;
    }

    /**
     * @inheritDoc
     */
    public function has($name)
    {
        return array_key_exists($name, $this->parameters);
    }

    /**
     * @inheritDoc
     */
    public function remove($name)
    {
        if ($this->has($name)) {
            unset($this->parameters[$name]);
        }
    }

    public function clear()
    {
        $this->parameters = [];
    }
}
