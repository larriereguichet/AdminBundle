<?php

namespace LAG\AdminBundle\Tests\Configuration;

use JK\Configuration\Exception\InvalidConfigurationException;
use LAG\AdminBundle\Admin\Action;
use LAG\AdminBundle\Admin\Configuration\AdminConfiguration;
use LAG\AdminBundle\Controller\AdminAction;
use LAG\AdminBundle\Exception\Exception;
use LAG\AdminBundle\Tests\TestCase;

// TODO trmove
class AdminConfigurationTest extends TestCase
{
    public function testDefaultConfiguration(): void
    {
        $configuration = new AdminConfiguration();
        $configuration->configure([
            'name' => 'my_admin',
            'entity' => 'MyEntity',
            'admin_class' => 'MyAdminClass',
        ]);

        $this->assertEquals([
            'actions' => [
                'create' => [],
                'update' => [
                    'route_parameters' => ['id' => null],
                ],
                'list' => [],
                'delete' => [
                    'route_parameters' => ['id' => null],
                ],
            ],
            'controller' => 'LAG\AdminBundle\Controller\AdminAction',
            'batch' => [],
            'admin_class' => 'MyAdminClass',
            'action_class' => 'LAG\AdminBundle\Admin\Action',
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
            'name' => 'my_admin',
            'entity' => 'MyEntity',
        ], $configuration->toArray());
    }

    public function testGetters(): void
    {
        $configuration = new AdminConfiguration();
        $configuration->configure([
            'name' => 'my_admin',
            'entity' => 'MyEntity',
            'admin_class' => 'MyAdminClass',
        ]);

        $this->assertEquals('my_admin', $configuration->getName());
        $this->assertEquals('MyEntity', $configuration->getEntityClass());
        $this->assertEquals([
            'create' => [],
            'update' => [
                'route_parameters' => ['id' => null],
            ],
            'list' => [],
            'delete' => [
                'route_parameters' => ['id' => null],
            ],
        ], $configuration->getActions());
        $this->assertEquals(true, $configuration->hasAction('create'));
        $this->assertEquals(false, $configuration->hasAction('wrong'));
        $this->assertEquals([], $configuration->getAction('create'));
        $this->assertEquals([], $configuration->getActionRouteParameters('create'));
        $this->assertEquals(['id' => null], $configuration->getActionRouteParameters('update'));

        $this->assertEquals(AdminAction::class, $configuration->getController());
        $this->assertEquals([], $configuration->getBatch());

        $this->assertEquals('MyAdminClass', $configuration->getAdminClass());
        $this->assertEquals(Action::class, $configuration->getActionClass());

        $this->assertEquals('lag_admin.{admin}.{action}', $configuration->getRoutesPattern());
        $this->assertEquals('pagerfanta', $configuration->getPager());
        $this->assertEquals(25, $configuration->getMaxPerPage());
        $this->assertEquals('page', $configuration->getPageParameter());

        $this->assertEquals(['ROLE_ADMIN'], $configuration->getPermissions());

        $this->assertEquals('Y-m-d', $configuration->getDateFormat());
        $this->assertEquals('doctrine', $configuration->getDataProvider());
        $this->assertEquals('doctrine', $configuration->getDataPersister());

        $this->assertEquals('@LAGAdmin/crud/create.html.twig', $configuration->getCreateTemplate());
        $this->assertEquals('@LAGAdmin/crud/update.html.twig', $configuration->getUpdateTemplate());
        $this->assertEquals('@LAGAdmin/crud/list.html.twig', $configuration->getListTemplate());
        $this->assertEquals('@LAGAdmin/crud/delete.html.twig', $configuration->getDeleteTemplate());
    }

    public function testGetPager(): void
    {
        $configuration = new AdminConfiguration();
        $configuration->configure([
            'name' => 'my_admin',
            'entity' => 'MyEntity',
            'pager' => false,
        ]);

        $this->expectException(Exception::class);
        $configuration->getPager();
    }

    public function testGetActions(): void
    {
        $configuration = new AdminConfiguration();
        $configuration->configure([
            'name' => 'my_admin',
            'entity' => 'MyEntity',
            'actions' => [
                'my_action' => null,
            ],
        ]);

        $this->assertEquals([], $configuration->getAction('my_action'));
    }

    public function testWithoutConfiguration(): void
    {
        $this->expectException(InvalidConfigurationException::class);
        $configuration = new AdminConfiguration();
        $configuration->configure([]);
    }

    public function testWithoutAdminPlaceHolder(): void
    {
        $this->expectException(InvalidConfigurationException::class);
        $configuration = new \LAG\AdminBundle\Admin\Configuration\AdminConfiguration();
        $configuration->configure([
            'name' => 'my_admin',
            'entity' => 'MyEntity',
            'routes_pattern' => 'test.{action}',
        ]);
    }

    public function testWithoutActionPlaceHolder(): void
    {
        $this->expectException(InvalidConfigurationException::class);
        $configuration = new AdminConfiguration();
        $configuration->configure([
            'name' => 'my_admin',
            'entity' => 'MyEntity',
            'routes_pattern' => 'test.{admin}',
        ]);
    }
}
