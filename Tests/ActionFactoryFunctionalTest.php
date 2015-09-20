<?php

namespace BlueBear\AdminBundle\Tests;


use BlueBear\AdminBundle\Admin\Action;
use BlueBear\AdminBundle\Admin\Admin;
use BlueBear\AdminBundle\Admin\Configuration\AdminConfiguration;
use BlueBear\AdminBundle\Admin\Configuration\ApplicationConfiguration;

class ActionFactoryFunctionalTest extends Base
{
    public function testCreate()
    {
        $client = $this->initApplication();
        $container = $client->getKernel()->getContainer();
        $actionFactory = $container->get('bluebear.admin.action_factory');
        $actionsConfiguration = $this->getFakeActionConfiguration();
        $adminConfiguration = new AdminConfiguration([
            'entity' => 'Test',
            'form' => 'test',
            'actions' => [
                'minimal_action' => [],
                'other_action' => [],
            ]
        ], new ApplicationConfiguration([], 'en'));
        $fakeAdmin = new Admin('action_test', null, null, $adminConfiguration);

        foreach ($actionsConfiguration as $actionName => $actionConfiguration) {
            $action = $actionFactory->create($actionName, $actionConfiguration, $fakeAdmin);
            $this->doTestActionForConfiguration($action, $actionConfiguration, $actionName);
        }
    }

    protected function getFakeActionConfiguration()
    {
        return [
            'minimal_action' => [
                'fields' => ['id' => []]
            ],
            'other_action' => [
                'title' => 'MyTitle',
                'fields' => ['id' => [], 'label' => []],
                'permissions' => ['MY_ROLE']
            ]
        ];
    }

    protected function doTestActionForConfiguration(Action $action, array $configuration, $actionName)
    {

            $this->assertEquals($actionName, $action->getName());

            if (array_key_exists('title', $configuration)) {
                // test configured title
                $this->assertEquals($configuration['title'], $action->getTitle());
            } else {
                // test default title
                $this->assertEquals('bluebear.admin.action_test.' . $actionName, $action->getTitle());
            }
            if (array_key_exists('fields', $configuration)) {
                $this->assertCount(count($configuration['fields']), $action->getFields());
            }
            if (array_key_exists('permissions', $configuration)) {
                $this->assertCount(count($configuration['permissions']), $action->getPermissions());
                $this->assertEquals($configuration['permissions'], $action->getPermissions());
            }


            /**
             * 'title' => null,
            'fields' => [
            'id' => []
            ],
            'field_actions' => [],
            'permissions' => ['ROLE_ADMIN'],
            'export' => [],
            'order' => [],
            'actions' => [],
            'target' => '_self',
            'route' => '',
            'parameters' => [],
            'icon' => null,
            'filters' => []
             */

    }
}
