<?php

namespace LAG\AdminBundle\Tests\AdminBundle\Action\Event;

use LAG\AdminBundle\Action\Configuration\ActionConfiguration;
use LAG\AdminBundle\Action\Event\BeforeConfigurationEvent;
use LAG\AdminBundle\Admin\AdminInterface;
use LAG\AdminBundle\Tests\AdminTestBase;

class BeforeConfigurationEventTest extends AdminTestBase
{
    public function testEvent()
    {
        $admin = $this->getMockWithoutConstructor(AdminInterface::class);
        
        $event = new BeforeConfigurationEvent('my-admin', [
            'what a super key',
        ], $admin);
    
        $this->assertEquals('my-admin', $event->getActionName());
        $this->assertEquals([
            'what a super key',
        ], $event->getActionConfiguration());
        $this->assertEquals($admin, $event->getAdmin());
        
        $configuration2 = $this->getMockWithoutConstructor(ActionConfiguration::class);
        $event->setActionConfiguration($configuration2);
    
        $this->assertEquals($configuration2, $event->getActionConfiguration());
    }
}
