<?php

namespace LAG\AdminBundle\Tests\AdminBundle\Action\Event;

use LAG\AdminBundle\Action\Event\ActionCreatedEvent;
use LAG\AdminBundle\Controller\ListAction;
use LAG\AdminBundle\Admin\AdminInterface;
use LAG\AdminBundle\Tests\AdminTestBase;

class ActionCreatedEventTest extends AdminTestBase
{
    public function testEvent()
    {
        $admin = $this->getMockWithoutConstructor(AdminInterface::class);
        $action = $this->getMockWithoutConstructor(ListAction::class);

        $event = new ActionCreatedEvent(
            $action,
            $admin
        );

        $this->assertEquals($action, $event->getAction());
        $this->assertEquals($admin, $event->getAdmin());
    }
}
