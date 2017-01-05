<?php

namespace LAG\AdminBundle\Tests\Fixtures\Action;

use LAG\AdminBundle\Action\ActionInterface;
use LAG\AdminBundle\Action\Configuration\ActionConfiguration;
use LAG\AdminBundle\Admin\AdminInterface;

class TestAction implements ActionInterface
{
    public function setConfiguration(ActionConfiguration $actionConfiguration)
    {
    }

    public function getConfiguration()
    {
    }

    public function isLoadingRequired()
    {
    }
    
    public function getName()
    {
    }

    public function setAdmin(AdminInterface $admin)
    {
    }

    public function getAdmin()
    {
    }
}
