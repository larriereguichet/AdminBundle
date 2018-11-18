<?php

namespace LAG\AdminBundle\Tests\Event\Menu;

use LAG\AdminBundle\Event\Menu\MenuConfigurationEvent;
use LAG\AdminBundle\Tests\AdminTestBase;

class MenuConfigurationTest extends AdminTestBase
{
    public function testGettersAndSetters()
    {
        $event = new MenuConfigurationEvent();
        $event->setMenuConfigurations([
            'panda' => 'bamboo',
        ]);

        $this->assertEquals([
            'panda' => 'bamboo',
        ], $event->getMenuConfigurations());
    }
}
