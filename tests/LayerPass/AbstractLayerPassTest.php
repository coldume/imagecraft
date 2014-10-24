<?php

namespace Imagecraft\LayerPass;

/**
 * @covers Imagecraft\LayerPass\AbstractLayerPass
 */
class AbstractLayerPassTest extends \PHPUnit_Framework_TestCase
{
    protected $pass;

    public function setUp()
    {
        $this->pass = $this->getMockForAbstractClass('Imagecraft\\LayerPass\\AbstractLayerPass');
    }

    public function testSanitizeHexColor()
    {
        $this->assertEquals(['hex' => '#000', 'rgb' => [0, 0, 0]], $this->pass->sanitizeHexColor('000'));
        $this->assertEquals(['hex' => '#C0C0C0', 'rgb' => [192, 192, 192]], $this->pass->sanitizeHexColor('#c0c0c0'));
    }

    /**
     * @expectedException Imagecraft\Exception\InvalidArgumentException
     */
    public function testSanitizeHexColorWhenColorIsInvalid()
    {
        $this->pass->sanitizeHexColor('foo');
    }

    public function testSanitizeEnumeration()
    {
        $this->assertEquals(1, $this->pass->sanitizeEnumeration(1, [1, 2, 3]));
    }

    /**
     * @expectedException Imagecraft\Exception\InvalidArgumentException
     */
    public function testSanitizeEnumerationWhenEnumerationIsInvalid()
    {
        $this->pass->sanitizeEnumeration(1, [2, 3, 4]);
    }

    public function testSanitizeUrl()
    {
        $this->assertEquals('http://www.example.com', $this->pass->sanitizeUrl('www.example.com'));
    }
}
