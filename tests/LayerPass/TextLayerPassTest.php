<?php

namespace Imagecraft\LayerPass;

/**
 * @covers Imagecraft\LayerPass\TextLayerPass
 */
class TextLayerPassTest extends \PHPUnit_Framework_TestCase
{
    protected $pass;

    protected $layer;

    public function setUp()
    {
        $this->pass  = $this->getMock('Imagecraft\\LayerPass\\TextLayerPass', null);
        $this->layer = $this->getMock('Imagecraft\\Layer\\TextLayer', null);
    }

    public function testProcessFont()
    {
        $this->layer->add([
            'text.font.filename'  => 'foo',
            'text.font.size'      => 15,
            'text.font.hex_color' => '#000',
        ]);
        $this->pass->processFont($this->layer);
        $this->assertEquals('foo', $this->layer->get('text.font.filename'));
        $this->assertEquals(15, $this->layer->get('text.font.size'));
        $this->assertEquals('#000', $this->layer->get('text.font.hex_color'));
        $this->assertEquals([0, 0, 0], $this->layer->get('text.font.rgb_color'));
    }

    /**
     * @expectedException Imagecraft\Exception\BadMethodCallException
     */
    public function testProcessFontWhenNoFontIsAdded()
    {
        $this->pass->processFont($this->layer);
    }

    public function testProcessLabel()
    {
        $this->pass->processLabel($this->layer);
        $this->assertEquals('', $this->layer->get('text.label'));

        $this->layer->clear();
        $this->layer->set('text.label', 'foo');
        $this->pass->processLabel($this->layer);
        $this->assertEquals('foo', $this->layer->get('text.label'));
    }

    public function testProcessAngle()
    {
        $this->pass->processAngle($this->layer);
        $this->assertEquals(0, $this->layer->get('text.angle'));

        $this->layer->clear();
        $this->layer->set('text.angle', 10.1);
        $this->pass->processAngle($this->layer);
        $this->assertEquals(10, $this->layer->get('text.angle'));
    }

    public function testProcessLineSpacing()
    {
        $this->pass->processLineSpacing($this->layer);
        $this->assertEquals(0.5, $this->layer->get('text.line_spacing'));

        $this->layer->clear();
        $this->layer->set('text.line_spacing', 10.1);
        $this->pass->processLineSpacing($this->layer);
        $this->assertEquals(10.1, $this->layer->get('text.line_spacing'));
    }

    public function testProcessBox()
    {
        $this->pass->processBox($this->layer);
        $this->assertEquals([0, 0, 0, 0], $this->layer->get('text.box.paddings'));
        $this->assertEquals(null, $this->layer->get('text.box.hex_color'));

        $this->layer->clear();
        $this->layer->add([
            'text.box.paddings'  => [10, 1],
            'text.box.hex_color' => '#000',
        ]);
        $this->pass->processBox($this->layer);
        $this->assertEquals([10, 1, 0, 0], $this->layer->get('text.box.paddings'));
        $this->assertEquals('#000', $this->layer->get('text.box.hex_color'));
        $this->assertEquals([0, 0, 0], $this->layer->get('text.box.rgb_color'));
    }
}
