<?php

namespace LAG\AdminBundle\Tests\AdminBundle\Message;

use LAG\AdminBundle\Message\MessageHandler;
use LAG\AdminBundle\Tests\Base;
use Symfony\Component\HttpFoundation\Session\Flash\FlashBag;

class MessageHandlerTest extends Base
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
}
