<?php

namespace LAG\AdminBundle\Tests\Event\Events;

use LAG\AdminBundle\Admin\AdminInterface;
use LAG\AdminBundle\Event\Events\ViewEvent;
use LAG\AdminBundle\Exception\View\MissingViewException;
use LAG\AdminBundle\Tests\TestCase;
use LAG\AdminBundle\View\ViewInterface;
use Symfony\Component\HttpFoundation\Request;

class ViewEventTest extends TestCase
{
    public function testEvent(): void
    {
        $admin = $this->createMock(AdminInterface::class);
        $request = new Request();

        $event = new ViewEvent($admin, $request);
        $this->assertExceptionRaised(MissingViewException::class, function () use ($event) {
            $event->getView();
        });

        $view = $this->createMock(ViewInterface::class);
        $event->setView($view);
        $this->assertEquals($view, $event->getView());
    }
}
