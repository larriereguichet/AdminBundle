<?php

namespace LAG\AdminBundle\Tests\Fixtures;

use LAG\AdminBundle\Admin\ActionInterface;
use LAG\AdminBundle\Configuration\ActionConfiguration;
use LAG\AdminBundle\Configuration\AdminConfiguration;
use LAG\AdminBundle\Configuration\ApplicationConfiguration;

class ActionFixture implements ActionInterface
{
    public function getName(): string
    {
        return 'test';
    }

    public function getConfiguration(): ActionConfiguration
    {
        return new ActionConfiguration('test', 'test', new AdminConfiguration('test', new ApplicationConfiguration()));
    }
}
