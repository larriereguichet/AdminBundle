<?php

namespace LAG\AdminBundle\Tests\AdminBundle\Action\Event;

use LAG\AdminBundle\Action\Event\ActionCreateEvent;
use LAG\AdminBundle\Admin\AdminInterface;
use LAG\AdminBundle\Tests\AdminTestBase;

class ActionCreateEventTest extends AdminTestBase
{
    public function testEvent()
    {
        $admin = $this->getMockWithoutConstructor(AdminInterface::class);

        $event = new ActionCreateEvent(
            'my_action',
            [],
            $admin
        );

        $this->assertEquals('my_action', $event->getActionName());
        $this->assertEquals([], $event->getActionConfiguration());
        $this->assertEquals($admin, $event->getAdmin());

        $event->setActionConfiguration([
            'test',
        ]);
        $this->assertEquals([
            'test',
        ], $event->getActionConfiguration());
    }
}
