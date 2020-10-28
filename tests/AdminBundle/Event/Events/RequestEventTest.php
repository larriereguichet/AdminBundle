<?php

namespace LAG\AdminBundle\Tests\Event\Events;

use LAG\AdminBundle\Admin\ActionInterface;
use LAG\AdminBundle\Admin\AdminInterface;
use LAG\AdminBundle\Event\Events\RequestEvent;
use LAG\AdminBundle\Exception\Action\MissingActionException;
use LAG\AdminBundle\Tests\TestCase;
use Symfony\Component\HttpFoundation\Request;

class RequestEventTest extends TestCase
{
    public function testEvent(): void
    {
        $admin = $this->createMock(AdminInterface::class);
        $request = new Request();

        $event = new RequestEvent($admin, $request);
        $this->assertExceptionRaised(MissingActionException::class, function () use ($event) {
            $event->getAction();
        });

        $action = $this->createMock(ActionInterface::class);
        $event->setAction($action);
        $this->assertEquals($action, $event->getAction());
    }
}
