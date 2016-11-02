<?php

namespace LAG\AdminBundle\Tests\AdminBundle\Action;

use LAG\AdminBundle\Action\Action;
use LAG\AdminBundle\Action\Configuration\ActionConfiguration;
use LAG\AdminBundle\Field\Field\StringField;
use LAG\AdminBundle\Tests\AdminTestBase;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ActionTest extends AdminTestBase
{
    public function testAction()
    {
        $resolver = new OptionsResolver();
        $configuration = new ActionConfiguration('test_action', $this->createAdmin('my_admin', [
            'entity' => 'WhatEver',
            'form' => 'WhatEver',
            'actions' => [
                'test_action' => []
            ]
        ]));
        $configuration->configureOptions($resolver);
        $resolvedConfiguration = $resolver->resolve();
        $configuration->setParameters($resolvedConfiguration);

        $action = new Action('test_action', $configuration);

        $this->assertEquals('test_action', $action->getName());
        $this->assertEquals($configuration, $action->getConfiguration());
        $this->assertEquals([], $action->getFields());
        $this->assertEquals([], $action->getFilters());
        $this->assertEquals(['ROLE_ADMIN'], $action->getPermissions());
        $this->assertFalse($action->hasField('a field'));

        $field = new StringField();
        $field->setName('name');
        $action->setFields([
            'name' => $field
        ]);

        $this->assertEquals([
            'name' => $field
        ], $action->getFields());
        $this->assertTrue($action->hasField('name'));
    }
}
