<?php

namespace Imagecraft\Engine\PhpGd\Extension\Core\EventListener;

/**
 * @covers Imagecraft\Engine\PhpGd\Extension\Core\EventListener\SystemRequirementListener
 */
class SystemRequirementListenerTest extends \PHPUnit_Framework_TestCase
{
    protected $context;

    protected $listener;

    protected $event;

    public function setUp()
    {
        $this->context  = $this->getMock('Imagecraft\\Engine\\PhpGd\\PhpGdContext', []);
        $this->listener = $this->getMock(
            'Imagecraft\\Engine\PhpGd\\Extension\\Core\\EventListener\\SystemRequirementListener',
            null,
            [$this->context]
        );
        $this->event = $this->getMock('Imagecraft\\Engine\\PhpGd\\PhpGdEvent', [], [], '', false);
    }

    /**
     * @expectedException Imagecraft\Exception\RuntimeException
     */
    public function testVerifyEngine()
    {
        $this->context
            ->expects($this->once())
            ->method('isEngineSupported')
            ->will($this->returnValue(false))
        ;
        $this->listener->verifyEngine();
    }

    /**
     * @expectedException Imagecraft\Exception\InvalidArgumentException
     */
    public function testVerifySavedFormatWhenFormatIsInvalid()
    {
        $this->context
            ->expects($this->once())
            ->method('isImageFormatSupported')
            ->will($this->returnValue(false))
        ;
        $this->event
            ->expects($this->once())
            ->method('getOptions')
            ->will($this->returnValue(['output_format' => 'foo']))
        ;
        $this->listener->verifySavedFormat($this->event);
    }
}
