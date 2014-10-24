<?php

namespace Imagecraft\Engine\PhpGd\Extension\Core;

/**
 * @covers Imagecraft\Engine\PhpGd\Extension\Core\CoreExtension
 */
class CoreExtensionTest extends \PHPUnit_Framework_TestCase
{
    public function testBoot()
    {
        $dispatcher = $this->getMock('Symfony\\Component\\EventDispatcher\\EventDispatcherInterface');
        $dispatcher
            ->expects($this->atLeastOnce())
            ->method('addSubscriber')
        ;
        $extension = $this->getMock('Imagecraft\\Engine\\PhpGd\\Extension\\Core\\CoreExtension', null);
        $extension->boot($dispatcher);
    }
}
