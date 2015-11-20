<?php

namespace LAG\AdminBundle\Tests\Functional;

use LAG\AdminBundle\Admin\ActionInterface;
use LAG\AdminBundle\Admin\Configuration\AdminConfiguration;
use LAG\AdminBundle\Admin\Configuration\ApplicationConfiguration;
use LAG\AdminBundle\Tests\Base;

class ActionFactoryFunctionalTest extends Base
{
    public function testCreate()
    {
        $this->initApplication();
        $actionFactory = $this
            ->container
            ->get('lag.admin.action_factory');
        $actionsConfiguration = $this->getFakeActionConfiguration();
        $adminConfiguration = new AdminConfiguration([
            'controller' => 'LAGAdminBundle:Generic',
            'manager' => 'LAG\AdminBundle\Manager\GenericManager',
            'entity' => 'Test',
            'form' => 'test',
            'actions' => [
                'minimal_action' => [],
                'other_action' => [],
                'full_action' => [],
            ],
            'max_per_page' => 50,
            'routing_url_pattern' => 'lag.admin.{admin}',
            'routing_name_pattern' => 'lag.{admin}.{action}'
        ], new ApplicationConfiguration([], 'en'));

        $fakeAdmin = $this->mokeAdmin('action_test', $adminConfiguration);

        foreach ($actionsConfiguration as $actionName => $actionConfiguration) {
            $action = $actionFactory->create($actionName, $actionConfiguration, $fakeAdmin);
            $this->doTestActionForConfiguration($action, $actionConfiguration, $actionName);
        }
    }

    protected function getFakeActionConfiguration()
    {
        return [
            'minimal_action' => [
                'fields' => ['id' => []],
            ],
            'other_action' => [
                'title' => 'MyTitle',
                'fields' => ['id' => [], 'label' => []],
                'permissions' => ['MY_ROLE'],
            ],
            'full_action' => [
                'title' => 'MyTitle',
                'fields' => [
                    'id' => [],
                    'label' => [
                        'type' => 'string',
                        'options' => [
                            'length' => 50,
                        ],
                    ],
                    'my_date' => [
                        'type' => 'date',
                        'options' => [
                            'format' => 'd/m/Y',
                        ],
                    ],
                ],
                'permissions' => ['MY_ROLE', 'AN_OTHER_ROLE'],
                'export' => ['json', 'xml'],
                'order' => [
                    'test_order' => 'asc',
                ],
                'actions' => [
                    'test' => [],
                ],
                'target' => [
                    '_blank',
                ],
                'route' => 'my.route',
                'parameters' => [
                    'id' => [],
                ],
                'icon' => 'an_icon',
                'filters' => [],
            ],
        ];
    }

    protected function doTestActionForConfiguration(ActionInterface $action, array $configuration, $actionName)
    {
        $this->assertEquals($actionName, $action->getName());

        if (array_key_exists('title', $configuration)) {
            // test configured title
            $this->assertEquals($configuration['title'], $action->getTitle());
        } else {
            // test default title
            $this->assertEquals('lag.admin.'.$actionName, $action->getTitle());
        }
        if (array_key_exists('fields', $configuration)) {
            // field creation will be tested more in FieldFactory test
            $this->assertCount(count($configuration['fields']), $action->getFields());
        }
        if (array_key_exists('permissions', $configuration)) {
            $this->assertEquals($configuration['permissions'], $action->getPermissions());
        }
        if (array_key_exists('export', $configuration)) {
            $this->assertEquals($configuration['export'], $action->getConfiguration()->getExports());
        }
        if (array_key_exists('order', $configuration)) {
            $this->assertCount(count($configuration['order']), $action->getConfiguration()->getOrder());
        }
        if (array_key_exists('actions', $configuration)) {
            // TODO improve linked action test
            $this->assertCount(count($configuration['actions']), $action->getActions());
        }
        if (array_key_exists('target', $configuration)) {
            $this->assertEquals($configuration['target'], $action->getConfiguration()->getTarget());
        } else {
            $this->assertEquals('_self', $action->getConfiguration()->getTarget());
        }
        if (array_key_exists('route', $configuration)) {
            $this->assertEquals($configuration['route'], $action->getConfiguration()->getRoute());
        }
        if (array_key_exists('parameters', $configuration)) {
            $this->assertCount(count($configuration['parameters']), $action->getConfiguration()->getParameters());
        }
        if (array_key_exists('icon', $configuration)) {
            $this->assertEquals($configuration['icon'], $action->getConfiguration()->getIcon());
        }
        if (array_key_exists('filters', $configuration)) {
            //$this->assertCount(count($configuration['filters']), $action->getFilters());
        }
    }
}
