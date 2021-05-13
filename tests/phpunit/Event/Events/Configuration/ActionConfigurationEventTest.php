<?php

namespace LAG\AdminBundle\Tests\Event\Events\Configuration;

use LAG\AdminBundle\Event\Events\Configuration\ActionConfigurationEvent;
use LAG\AdminBundle\Tests\TestCase;

class ActionConfigurationEventTest extends TestCase
{
    public function testEvent(): void
    {
        $event = new ActionConfigurationEvent('my_action', [
            'a_parameter' => true,
        ]);

        $this->assertEquals('my_action', $event->getActionName());
        $this->assertEquals(['a_parameter' => true], $event->getConfiguration());
    }
}
