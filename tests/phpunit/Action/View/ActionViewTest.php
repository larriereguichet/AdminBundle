<?php

namespace LAG\AdminBundle\Tests\Action\View;

use LAG\AdminBundle\Action\Configuration\ActionConfiguration;
use LAG\AdminBundle\Action\View\ActionView;
use LAG\AdminBundle\Tests\TestCase;

class ActionViewTest extends TestCase
{
    public function testView(): void
    {
        $configuration = new ActionConfiguration();
        $configuration->configure([
            'name' => 'index',
            'admin_name' => 'projects',
            'route' => 'test',
        ]);
        $view = new ActionView('index', $configuration);

        $this->assertEquals('index', $view->getName());
        $this->assertEquals('Index', $view->getTitle());
        $this->assertEquals($configuration, $view->getConfiguration());
    }
}
