<?php

namespace LAG\AdminBundle\Tests\Action;

use LAG\AdminBundle\Action\Action;
use LAG\AdminBundle\Action\Configuration\ActionConfiguration;
use LAG\AdminBundle\Action\View\ActionView;
use LAG\AdminBundle\Tests\TestCase;

class ActionTest extends TestCase
{
    public function testGetters(): void
    {
        $configuration = $this->createMock(ActionConfiguration::class);
        $action = new Action('test', $configuration);

        $this->assertEquals('test', $action->getName());
        $this->assertEquals($configuration, $action->getConfiguration());
        $this->assertEquals(new ActionView('test', $action->getConfiguration()), $action->createView());
    }
}
