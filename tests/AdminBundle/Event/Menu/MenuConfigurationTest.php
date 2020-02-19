<?php

namespace LAG\AdminBundle\Tests\Event\Menu;

use LAG\AdminBundle\Event\Menu\MenuConfigurationEvent;
use LAG\AdminBundle\Tests\AdminTestBase;

class MenuConfigurationTest extends AdminTestBase
{
    public function testGettersAndSetters()
    {
        $event = new MenuConfigurationEvent('my_little_menu', [
            'thing' => 'maybe',
        ]);
        $this->assertEquals('my_little_menu', $event->getMenuName());
        $this->assertEquals([
            'thing' => 'maybe',
        ], $event->getMenuConfiguration());

        $event->setMenuConfiguration([
            'thing' => 'sure',
        ]);
        $this->assertEquals([
            'thing' => 'sure',
        ], $event->getMenuConfiguration());
    }
}
