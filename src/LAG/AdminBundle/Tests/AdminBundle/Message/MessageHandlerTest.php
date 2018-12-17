<?php

namespace LAG\AdminBundle\Tests\AdminBundle\Message;

use LAG\AdminBundle\Message\MessageHandler;
use LAG\AdminBundle\Tests\AdminTestBase;
use Psr\Log\LoggerInterface;
use Symfony\Bundle\FrameworkBundle\Translation\Translator;
use Symfony\Component\HttpFoundation\Session\Flash\FlashBag;
use Symfony\Component\HttpFoundation\Session\Session;

class MessageHandlerTest extends AdminTestBase
{
    public function testHandleError()
    {
        $loggerMock = $this->mockLogger();
        $loggerMock
            ->expects($this->once())
            ->method('error')
        ;
        $sessionMock = $this->mockSession();
        $sessionMock
            ->expects($this->exactly(2))
            ->method('getFlashBag')
            ->willReturn(new FlashBag())
        ;
        $translatorMock = $this->mockTranslator();
        $translatorMock
            ->expects($this->exactly(2))
            ->method('trans')
            ->willReturn('test')
        ;

        $messageHandler = new MessageHandler($loggerMock, $sessionMock, $translatorMock);
        $messageHandler->handleError('test', 'test');
        $messageHandler->handleError('test');
    }

    public function testHandleSuccess()
    {
        $logger = $this->getMockWithoutConstructor(LoggerInterface::class);
        $logger
            ->expects($this->once())
            ->method('info')
            ->with('Wookie rocks')
        ;

        $flashBag = $this->getMockWithoutConstructor(FlashBag::class);
        $flashBag
            ->expects($this->once())
            ->method('add')
            ->with('info', 'Wookie rocks')
        ;

        $session = $this->getMockWithoutConstructor(Session::class);
        $session
            ->expects($this->once())
            ->method('getFlashBag')
            ->willReturn($flashBag)
        ;
        $translator = $this->getMockWithoutConstructor(Translator::class);
        $translator
            ->expects($this->once())
            ->method('trans')
            ->with('wookie.rocks')
            ->willReturn('Wookie rocks')
        ;

        $messageHandler = new MessageHandler($logger, $session, $translator);
        $messageHandler->handleSuccess('wookie.rocks', 'Wookie rocks');
    }
}
