<?php

namespace LAG\AdminBundle\Tests\Action;

use LAG\AdminBundle\Admin\Action;
use LAG\AdminBundle\Configuration\ActionConfiguration;
use LAG\AdminBundle\Tests\AdminTestBase;

class ActionTest extends AdminTestBase
{
    public function testGetters()
    {
        $configuration = $this->getMockWithoutConstructor(ActionConfiguration::class);
        $action = new Action('test', $configuration);

        $this->assertEquals('test', $action->getName());
        $this->assertEquals($configuration, $action->getConfiguration());
    }
}
