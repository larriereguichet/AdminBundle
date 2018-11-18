<?php

namespace LAG\AdminBundle\Tests\Fixtures;

use LAG\AdminBundle\Admin\ActionInterface;
use LAG\AdminBundle\Configuration\ActionConfiguration;

class ActionFixture implements ActionInterface
{
    public function getName(): string
    {
        return 'test';
    }

    public function getConfiguration(): ActionConfiguration
    {
    }
}
