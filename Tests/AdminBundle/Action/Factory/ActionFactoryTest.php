<?php

namespace LAG\AdminBundle\Tests\AdminBundle\Action\Factory;

use Exception;
use LAG\AdminBundle\Action\Factory\ActionFactory;
use LAG\AdminBundle\Action\ActionInterface;
use LAG\AdminBundle\Admin\Admin;
use LAG\AdminBundle\Admin\Filter;
use LAG\AdminBundle\Field\Field\StringField;
use LAG\AdminBundle\Tests\AdminTestBase;
use Symfony\Component\EventDispatcher\EventDispatcher;

class ActionFactoryTest extends AdminTestBase
{
    public function testCreate()
    {
        $field = new StringField();
        $field->setName('my_field');

        $actionFactory = $this->initActionFactory();
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

        // default action load strategy SHOULD be unique for create action
        $this->assertEquals(Admin::LOAD_STRATEGY_UNIQUE, $action->getConfiguration()->getParameter('load_strategy'));
    }

    public function testCreatePermissions()
    {
        $actionFactory = $this->initActionFactory();
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
            ->assertExceptionRaised(Exception::class, function () use ($actionFactory, $admin) {
                $actionFactory->create('test_action', [], $admin);
            });
    }

    public function testCreateWithCriteria()
    {
        $actionFactory = $this->initActionFactory();
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
    }

    /**
     * @return ActionFactory
     */
    protected function initActionFactory()
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
            $this->createConfigurationFactory(),
            new EventDispatcher()
        );

        return $actionFactory;
    }
}
