<?php

namespace LAG\AdminBundle\Tests\Event\Events\Configuration;

use LAG\AdminBundle\Event\Events\Configuration\AdminConfigurationEvent;
use LAG\AdminBundle\Tests\TestCase;

class AdminConfigurationEventTest extends TestCase
{
    public function testEvent(): void
    {
        $event = new AdminConfigurationEvent('my_admin', [
            'a_parameter' => true,
        ]);

        $this->assertEquals('my_admin', $event->getAdminName());
        $this->assertEquals(['a_parameter' => true], $event->getConfiguration());

        $event->setConfiguration(['another_parameter' => false]);
        $this->assertEquals(['another_parameter' => false], $event->getConfiguration());
    }
}
