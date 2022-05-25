<?php

namespace LAG\AdminBundle\Tests\Admin;

use LAG\AdminBundle\Action\Action;
use LAG\AdminBundle\Admin\Admin;
use LAG\AdminBundle\Admin\Configuration\AdminConfiguration;
use LAG\AdminBundle\Controller\AdminAction;
use LAG\AdminBundle\Tests\TestCase;

class AdminConfigurationTest extends TestCase
{
    public function testDefaultConfiguration(): void
    {
        $this->assertEquals([
            'name' => 'my_admin',
            'entity' => 'MyEntity',
            'actions' => [
                'create' => [
                    'admin_name' => 'my_admin',
                ],
                'update' => [
                    'admin_name' => 'my_admin',
                    'route_parameters' => ['id' => null],
                ],
                'index' => [
                    'admin_name' => 'my_admin',
                ],
                'delete' => [
                    'admin_name' => 'my_admin',
                    'route_parameters' => ['id' => null],
                ],
            ],
            'controller' => AdminAction::class,
            'batch' => [],
            'admin_class' => Admin::class,
            'action_class' => Action::class,
            'routes_pattern' => 'lag_admin.{admin}.{action}',
            'pager' => 'pagerfanta',
            'max_per_page' => 25,
            'page_parameter' => 'page',
            'permissions' => 'ROLE_ADMIN',
            'date_format' => 'Y-m-d',
            'data_provider' => 'doctrine',
            'data_persister' => 'doctrine',
            'create_template' => '@LAGAdmin/crud/create.html.twig',
            'update_template' => '@LAGAdmin/crud/update.html.twig',
            'list_template' => '@LAGAdmin/crud/list.html.twig',
            'delete_template' => '@LAGAdmin/crud/delete.html.twig',
            'title' => 'MyAdmin',
            'index_actions' => [
                'update' => [
                    'route' => null,
                    'route_parameters' => [],
                    'admin' => 'my_admin',
                    'url' => null,
                    'action' => 'update',
                    'text' => 'lag_admin.actions.update',
                    'attr' => [],
                ],
                'delete' => [
                    'route' => null,
                    'route_parameters' => [],
                    'admin' => 'my_admin',
                    'url' => null,
                    'action' => 'delete',
                    'text' => 'lag_admin.actions.delete',
                    'attr' => [],
                ],
            ],
            'item_actions' => [
                'create' => [
                    'route' => null,
                    'route_parameters' => [],
                    'admin' => 'my_admin',
                    'url' => null,
                    'action' => 'create',
                    'text' => 'lag_admin.actions.create',
                    'attr' => [],
                ],
            ],
        ], $this->createAdminConfiguration([
            'name' => 'my_admin',
            'entity' => 'MyEntity',
        ])->toArray());
    }

    private function createAdminConfiguration(array $options): AdminConfiguration
    {
        $configuration = new AdminConfiguration();
        $configuration->configure($options);

        return $configuration;
    }
}
