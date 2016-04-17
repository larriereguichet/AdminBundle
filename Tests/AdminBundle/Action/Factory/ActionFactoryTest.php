<?php

namespace LAG\AdminBundle\Tests\AdminBundle\Action\Factory;

use LAG\AdminBundle\Action\Factory\ActionFactory;
use LAG\AdminBundle\Admin\ActionInterface;
use LAG\AdminBundle\Admin\Admin;
use LAG\AdminBundle\Admin\Filter;
use LAG\AdminBundle\Field\Field\StringField;
use LAG\AdminBundle\Tests\AdminTestBase;

class ActionFactoryTest extends AdminTestBase
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
            $this->createConfigurationFactory()
        );

        // TEST 1 : permissions
        $admin = $this->createAdmin(
            'test_admin', [
                'controller' => 'test',
                'entity' => 'test',
                'form' => 'test',
                'actions' => [],
                'max_per_page' => 'test',
                'routing_name_pattern' => 'test',
                'routing_url_pattern' => 'test',
                'data_provider' => 'test',
            ]
        );

        // create method SHOULD throw an exception if the action is not granted
        $this
            ->assertExceptionRaised(\Exception::class, function () use ($actionFactory, $admin) {
                $actionFactory->create('test_action', [], $admin);
            });
        // END TEST 1

        // TEST 2 : creation
        $admin = $this->createAdmin(
            'test_admin', [
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
            ]
        );
        $action = $actionFactory->create('test_action', [
            'title' => 'My Title',
            'permissions' => ['ROLE_TEST'],
            'route' => 'test'
        ], $admin);

        $this->assertInstanceOf(ActionInterface::class, $action);
        $this->assertEquals('test_action', $action->getName());
        $this->assertEquals('My Title', $action->getTitle());
        $this->assertEquals([
            'ROLE_TEST'
        ], $action->getPermissions());
        $this->assertEquals([
            'my_field' => $field
        ], $action->getFields());
        // END TEST 2

        $admin = $this->createAdmin(
            'test_admin',
            [
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
            ]

        );
        $admin->addAction($this->createAction('edit', $admin));
        $action = $actionFactory->create('edit', [
            'title' => 'My Title',
            'permissions' => ['ROLE_TEST'],
        ], $admin);

        $this->assertEquals([
            'id'
        ], $action->getConfiguration()->getParameter('criteria'));

        $action = $actionFactory->create('delete', [
            'title' => 'My Title',
            'permissions' => ['ROLE_TEST'],
        ], $admin);

        $this->assertEquals([
            'id'
        ], $action->getConfiguration()->getParameter('criteria'));

        $action = $actionFactory->create('create', [
            'title' => 'My Title',
            'permissions' => ['ROLE_TEST'],
        ], $admin);

        // default action load strategy SHOULD be unique
        $this->assertEquals(Admin::LOAD_STRATEGY_UNIQUE, $action->getConfiguration()->getParameter('load_strategy'));
    }
}
