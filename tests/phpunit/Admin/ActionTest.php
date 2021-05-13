<?php

namespace LAG\AdminBundle\Tests\Admin;

use LAG\AdminBundle\Admin\Action;
use LAG\AdminBundle\Configuration\ActionConfiguration;
use LAG\AdminBundle\Tests\TestCase;

class ActionTest extends TestCase
{
    public function testGetters()
    {
        $configuration = $this->createMock(ActionConfiguration::class);
        $action = new Action('test', $configuration);

        $this->assertEquals('test', $action->getName());
        $this->assertEquals($configuration, $action->getConfiguration());
    }
}
