<?php

namespace LAG\AdminBundle\Tests\AdminBundle\Action\Configuration;

use LAG\AdminBundle\Action\Configuration\ActionConfiguration;
use LAG\AdminBundle\Tests\AdminTestBase;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ActionConfigurationTest extends AdminTestBase
{
    public function testActionConfiguration()
    {
        $admin = $this->createAdmin('an_admin', [
            'entity' => 'WhatEver',
            'form' => 'AForm',
            'actions' => [
                'an_action' => []
            ]
        ]);

        $resolver = new OptionsResolver();

        $configuration = new ActionConfiguration('an_action', $admin);
        $configuration->configureOptions($resolver);

        $resolver->resolve([
            'order' => [
                'test' => 'asc'
            ]
        ]);
    }
}
