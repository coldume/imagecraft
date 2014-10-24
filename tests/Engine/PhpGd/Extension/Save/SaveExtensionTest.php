<?php

namespace Imagecraft\Engine\PhpGd\Extension\Save;

/**
 * @covers Imagecraft\Engine\PhpGd\Extension\Save\SaveExtension
 */
class SaveExtensionTest extends \PHPUnit_Framework_TestCase
{
    public function testBoot()
    {
        $dispatcher = $this->getMock('Symfony\\Component\\EventDispatcher\\EventDispatcherInterface');
        $dispatcher
            ->expects($this->atLeastOnce())
            ->method('addSubscriber')
        ;
        $extension = $this->getMock('Imagecraft\\Engine\\PhpGd\\Extension\\Save\\SaveExtension', null);
        $extension->boot($dispatcher);
    }
}
