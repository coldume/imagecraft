<?php

namespace Imagecraft;

/**
 * @covers Imagecraft\AbstractContext
 */
class AbstractContextTest extends \PHPUnit_Framework_TestCase
{
    protected $context;

    public function setUp()
    {
        $this->context = $this->getMockForAbstractClass('Imagecraft\\AbstractContext');
    }

    public function testGetMemoryLimit()
    {
        $previous = ini_get('memory_limit');

        ini_set('memory_limit', '40M');
        $this->assertEquals(40 * 1024 * 1024, $this->context->getMemoryLimit());

        ini_set('memory_limit', '-1');
        $this->assertEquals(1024 * 1024 * 1024, $this->context->getMemoryLimit());

        ini_set('memory_limit', '40M');
        $this->assertEquals(35 * 1024 * 1024, $this->context->getMemoryLimit(-5));

        ini_set('memory_limit', '40M');
        $this->assertEquals(35 * 1024 * 1024, $this->context->getMemoryLimit(35));

        ini_set('memory_limit', $previous);
    }

    /*
     * @requires extension fileinfo
     */
    public function testIsFileinfoExtensionEnabled()
    {
        $this->assertTrue($this->context->isFileinfoExtensionEnabled());
    }
}
