<?php

namespace Imagecraft\LayerPass;

use Imagecraft\Layer\ImageAwareLayerInterface;

/**
 * @covers Imagecraft\LayerPass\ImageAwareLayerPass
 */
class ImageAwareLayerPassTest extends \PHPUnit_Framework_TestCase
{
    protected $pass;

    protected $layer;

    public function setUp()
    {
        $this->pass  = $this->getMock('Imagecraft\\LayerPass\\ImageAwareLayerPass', null);
        $this->layer = $this->getMock('Imagecraft\\Layer\\ImageLayer', null);
    }

    public function testProcessResource()
    {
        $this->layer->add(['image.http.url' => 'www.example.com', 'image.http.data_limit' => -10, 'timeout' => -20]);
        $this->pass->processResource($this->layer);
        $this->assertEquals($this->layer->get('image.http.url'), 'http://www.example.com');
        $this->assertEquals($this->layer->get('image.http.data_limit'), -1);
        $this->assertEquals($this->layer->get('image.http.timeout'), -1);

        $this->layer->clear();
        $this->layer->set('image.filename', 'foo');
        $this->pass->processResource($this->layer);
        $this->assertEquals($this->layer->get('image.filename'), 'foo');

        $this->layer->clear();
        $this->layer->set('image.contents', 'foo');
        $this->pass->processResource($this->layer);
        $this->assertEquals($this->layer->get('image.contents'), 'foo');
    }

    /**
     * @expectedException Imagecraft\Exception\BadMethodCallException
     */
    public function testProcessWhenNoResourceIsFound()
    {
        $this->pass->processResource($this->layer);
    }

    public function testProcessResize()
    {
        $this->layer->add([
            'image.resize.width'  => -5,
            'image.resize.height' => 0,
            'image.resize.option' => ImageAwareLayerInterface::RESIZE_SHRINK,
        ]);
        $this->pass->processResize($this->layer);
        $this->assertEquals(1, $this->layer->get('image.resize.width'));
        $this->assertEquals(1, $this->layer->get('image.resize.height'));
        $this->assertEquals(ImageAwareLayerInterface::RESIZE_SHRINK, $this->layer->get('image.resize.option'));
    }
}
