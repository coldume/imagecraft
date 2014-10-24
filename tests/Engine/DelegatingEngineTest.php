<?php

namespace Imagecraft\Engine\DelegatingEngine;

use Imagecraft\Engine\Fixtures\FooEngine;

/**
 * @covers Imagecraft\Engine\DelegatingEngine
 */
class DelegatingEngine extends \PHPUnit_Framework_TestCase
{
    protected $engine;

    public function setUp()
    {
        $this->engine = $this->getMock('Imagecraft\\Engine\\DelegatingEngine', ['getRegisteredEngines']);
        $this->engine
            ->method('getRegisteredEngines')
            ->will($this->returnValue(['foo' => new FooEngine()]))
        ;
    }

    public function testGetImage()
    {
        $this->assertEquals('foo', $this->engine->getImage([], ['engine' => 'foo']));
    }

    public function testGetContext()
    {
        $this->assertEquals('bar', $this->engine->getContext(['engine' => 'foo']));
    }
}
