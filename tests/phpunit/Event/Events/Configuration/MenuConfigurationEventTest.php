<?php

namespace LAG\AdminBundle\Tests\Event\Events\Configuration;

use LAG\AdminBundle\Event\Events\Configuration\MenuConfigurationEvent;
use LAG\AdminBundle\Tests\TestCase;

class MenuConfigurationEventTest extends TestCase
{
    public function testEvent(): void
    {
        $event = new MenuConfigurationEvent('my_admin', [
            'a_parameter' => true,
        ]);

        $this->assertEquals('my_admin', $event->getMenuName());
        $this->assertEquals(['a_parameter' => true], $event->getMenuConfiguration());

        $event->setMenuConfiguration(['another_parameter' => false]);
        $this->assertEquals(['another_parameter' => false], $event->getMenuConfiguration());
    }
}
