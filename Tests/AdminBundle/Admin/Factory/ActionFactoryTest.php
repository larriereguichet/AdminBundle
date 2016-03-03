<?php

namespace LAG\AdminBundle\Tests\AdminBundle\Admin\Factory;

use LAG\AdminBundle\Admin\ActionInterface;
use LAG\AdminBundle\Admin\Admin;
use LAG\AdminBundle\Admin\Configuration\AdminConfiguration;
use LAG\AdminBundle\Admin\Configuration\ApplicationConfiguration;
use LAG\AdminBundle\Admin\Factory\ActionFactory;
use LAG\AdminBundle\Admin\Filter;
use LAG\AdminBundle\Field\Field\StringField;
use LAG\AdminBundle\Tests\Base;

class ActionFactoryTest extends Base
{
    public function testCreate()
    {
        $field = new StringField();
        $field->setName('my_field');
        $fieldFactory = $this->mockFieldFactory();
        $fieldFactory
            ->method('create')
            ->willReturn($field);

        $filterFactory = $this->mockFilterFactory();
        $filterFactory
            ->method('create')
            ->willReturn(new Filter());

        $actionFactory = new ActionFactory(
            $fieldFactory,
            $filterFactory,
            new ApplicationConfiguration([], 'fr')
        );

        $admin = $this->mockAdmin(
            'test_admin',
            new AdminConfiguration([
                    'controller' => 'test',
                    'entity' => 'test',
                    'form' => 'test',
                    'actions' => [],
                    'max_per_page' => 'test',
                    'routing_name_pattern' => 'test',
                    'routing_url_pattern' => 'test',
                    'data_provider' => 'test',
                    'metadata' => 'test',
                ]
            )
        );

        // create method SHOULD throw an exception if the action is not granted
        $this
            ->assertExceptionRaised(\Exception::class, function () use ($actionFactory, $admin) {
                $actionFactory->create('test_action', [

                ], $admin);
            });

        $admin = $this->mockAdmin(
            'test_admin',
            new AdminConfiguration([
                    'controller' => 'test',
                    'entity' => 'test',
                    'form' => 'test',
                    'actions' => [
                        'test_action' => []
                    ],
                    'max_per_page' => 'test',
                    'routing_name_pattern' => 'test',
                    'routing_url_pattern' => 'test',
                    'data_provider' => 'test',
                    'metadata' => 'test'
                ]
            )
        );
        $action = $actionFactory->create('test_action', [
            'title' => 'My Title',
            'permissions' => ['ROLE_TEST'],
            'actions' => [
                'test' => []
            ],
            'filters' => [
                'test' => []
            ],
            'batch' => 'test',
            'route' => 'test'
        ], $admin);

        $this->assertInstanceOf(ActionInterface::class, $action);
        $this->assertEquals('test_action', $action->getName());
        $this->assertEquals('My Title', $action->getTitle());
        $this->assertEquals(['ROLE_TEST'], $action->getPermissions());
        $this->assertEquals([
            'my_field' => $field
        ], $action->getFields());
        $this->assertInstanceOf(ActionInterface::class, $action->getActions()['test']);

        $admin = $this->mockAdmin(
            'test_admin',
            new AdminConfiguration([
                    'controller' => 'test',
                    'entity' => 'test',
                    'form' => 'test',
                    'actions' => [
                        'edit' => [],
                        'delete' => [],
                        'create' => []
                    ],
                    'max_per_page' => 'test',
                    'routing_name_pattern' => 'test',
                    'routing_url_pattern' => 'test',
                    'data_provider' => 'test',
                    'metadata' => 'test',
                ]
            )
        );
        $action = $actionFactory->create('edit', [
            'title' => 'My Title',
            'permissions' => ['ROLE_TEST'],
            'actions' => [
                'test' => []
            ],
            'filters' => [
                'test' => []
            ],
            'batch' => [
                0 => null
            ]
        ], $admin);

        $this->assertEquals([
            'id'
        ], $action->getConfiguration()->getCriteria());

        $action = $actionFactory->create('delete', [
            'title' => 'My Title',
            'permissions' => ['ROLE_TEST'],
            'actions' => [
                'test' => []
            ],
            'filters' => [
                'test' => []
            ]
        ], $admin);

        $this->assertEquals([
            'id'
        ], $action->getConfiguration()->getCriteria());

        $action = $actionFactory->create('create', [
            'title' => 'My Title',
            'permissions' => ['ROLE_TEST'],
            'actions' => [
                'test' => []
            ],
            'filters' => [
                'test' => []
            ]
        ], $admin);

        $this->assertEquals(Admin::LOAD_STRATEGY_NONE, $action->getConfiguration()->getLoadStrategy());
    }
}
