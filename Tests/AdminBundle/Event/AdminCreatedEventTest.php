<?php

namespace LAG\AdminBundle\Tests\AdminBundle\Event;

use LAG\AdminBundle\Admin\Event\AdminCreatedEvent;
use LAG\AdminBundle\Tests\AdminTestBase;

class AdminCreatedEventTest extends AdminTestBase
{
    public function testEvent()
    {
        $admin = $this->createAdmin('my_admin', [
            'entity' => 'my_entity'
        ]);
        $event = new AdminCreatedEvent($admin);

        $this->assertEquals($admin, $event->getAdmin());
    }
}
