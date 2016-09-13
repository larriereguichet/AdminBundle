<?php

namespace LAG\AdminBundle\Tests\AdminBundle\Action\Event;

use LAG\AdminBundle\Action\Event\ActionCreatedEvent;
use LAG\AdminBundle\Tests\AdminTestBase;

class ActionCreatedEventTest extends AdminTestBase
{
    public function testEvent()
    {
        $admin = $this->createAdmin('my_admin', [
            'entity' => 'my_entity',
            'actions' => [
                'my_action' => '~'
            ]
        ]);
        $action = $this->createAction('my_action', $admin);
        $event = new ActionCreatedEvent(
            $action,
            $admin
        );

        $this->assertEquals($action, $event->getAction());
        $this->assertEquals($admin, $event->getAdmin());
    }
}
