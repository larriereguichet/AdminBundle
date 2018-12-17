<?php

namespace LAG\AdminBundle\Tests\AdminBundle\Event;

use LAG\AdminBundle\Admin\AdminInterface;
use LAG\AdminBundle\Admin\Event\AdminInjectedEvent;
use LAG\AdminBundle\Tests\AdminTestBase;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class AdminInjectedEventTest extends AdminTestBase
{
    public function testGetters()
    {
        $admin = $this->getMockWithoutConstructor(AdminInterface::class);
        $controller = $this->getMockWithoutConstructor(Controller::class);

        $event = new AdminInjectedEvent($admin, $controller);

        $this->assertEquals($admin, $event->getAdmin());
        $this->assertEquals($controller, $event->getController());
    }
}
