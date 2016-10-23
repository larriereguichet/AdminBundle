<?php

namespace LAG\AdminBundle\Tests\AdminBundle\Event;

use LAG\AdminBundle\Admin\Event\AdminCreateEvent;
use LAG\AdminBundle\Tests\AdminTestBase;

class AdminCreateEventTest extends AdminTestBase
{
    public function testEvent()
    {
        $event = new AdminCreateEvent('my_event', [
            'my_key' => 'value'
        ]);

        $this->assertEquals('my_event', $event->getAdminName());
        $this->assertEquals([
            'my_key' => 'value'
        ], $event->getAdminConfiguration());

        $event->setAdminConfiguration([
            'test'
        ]);
        $this->assertEquals([
            'test'
        ], $event->getAdminConfiguration());
    }
}
