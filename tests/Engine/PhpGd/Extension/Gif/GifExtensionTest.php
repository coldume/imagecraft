<?php

namespace Imagecraft\Engine\PhpGd\Extension\Gif;

/**
 * @covers Imagecraft\Engine\PhpGd\Extension\Gif\GifExtension
 */
class GifExtensionTest extends \PHPUnit_Framework_TestCase
{
    public function testBoot()
    {
        $dispatcher = $this->getMock('Symfony\\Component\\EventDispatcher\\EventDispatcherInterface');
        $dispatcher
            ->expects($this->atLeastOnce())
            ->method('addSubscriber')
        ;
        $extension = $this->getMock('Imagecraft\\Engine\\PhpGd\\Extension\\Gif\\GifExtension', null);
        $extension->boot($dispatcher);
    }
}
