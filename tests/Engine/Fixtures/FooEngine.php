<?php

namespace Imagecraft\Engine\Fixtures;

use Imagecraft\Engine\EngineInterface;

class FooEngine implements EngineInterface
{
    public function getImage(array $layers, array $options)
    {
        return 'foo';
    }

    public function getContext(array $options)
    {
        return 'bar';
    }
}
