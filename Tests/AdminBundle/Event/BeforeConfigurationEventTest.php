<?php

namespace LAG\AdminBundle\Tests\AdminBundle\Event;

use LAG\AdminBundle\Admin\Event\BeforeConfigurationEvent;
use LAG\AdminBundle\Tests\AdminTestBase;

class BeforeConfigurationEventTest extends AdminTestBase
{
    public function testEvent()
    {
        $configuration = [
            'admin1' => [
                'test' => 'ours'
            ],
            'admin2' => []
        ];
        $event = new BeforeConfigurationEvent($configuration);

        $this->assertEquals($configuration, $event->getAdminConfigurations());
    }
}
