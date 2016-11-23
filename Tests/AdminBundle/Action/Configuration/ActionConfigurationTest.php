<?php

namespace LAG\AdminBundle\Tests\AdminBundle\Action\Configuration;

use LAG\AdminBundle\Action\Configuration\ActionConfiguration;
use LAG\AdminBundle\Action\Configuration\ConfigurationException;
use LAG\AdminBundle\Tests\AdminTestBase;
use Symfony\Component\OptionsResolver\Exception\InvalidOptionsException;
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

        $options = $resolver->resolve([
            'order' => [
                'test' => 'asc'
            ]
        ]);
    }

    public function testFieldNormalizer()
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

        $options = $resolver->resolve([
            'fields' => [
                // test default field mapping
                'aField' => null
            ]
        ]);

        $this->assertEquals([], $options['fields']['aField']);
    }

    public function testRouteNormalizer()
    {
        $resolver = new OptionsResolver();
        $configuration = new ActionConfiguration('an_action');
        $configuration->configureOptions($resolver);

        // an exception should be thrown if no admin is passed to generate the route
        $this->assertExceptionRaised(InvalidOptionsException::class, function () use ($resolver) {
            $resolver->resolve([
                'route' => ''
            ]);
        });
    }

    public function testOrderNormalizer()
    {
        $resolver = new OptionsResolver();
        $configuration = new ActionConfiguration('an_action');
        $configuration->configureOptions($resolver);

        // an exception should be thrown if an invalid order configuration is passed
        $this->assertExceptionRaised(ConfigurationException::class, function () use ($resolver) {
            $resolver->resolve([
                'order' => [
                    'id' => 'ASK',
                ]
            ]);
        });
    }
}
