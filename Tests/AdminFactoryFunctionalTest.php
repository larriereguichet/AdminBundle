<?php

namespace BlueBear\AdminBundle\Tests;

use BlueBear\AdminBundle\Admin\Admin;
use BlueBear\AdminBundle\Admin\Factory\AdminFactory;
use Symfony\Component\HttpFoundation\Request;

class AdminFactoryFunctionalTest extends Base
{
    public function testInitNoParameters()
    {
        $client = $this->initApplication();
        $container = $client->getKernel()->getContainer();
        // admin factory initialization
        $adminFactory = new AdminFactory($container);
        $this->assertTrue($adminFactory != null, 'AdminFactory initialization error');
        $this->assertCount(0, $adminFactory->getAdmins(), 'No admin should be found');
    }

    public function testCreateAdminFromConfiguration()
    {
        $client = $this->initApplication();
        $container = $client->getKernel()->getContainer();
        $adminFactory = new AdminFactory($container);
        $config = $this->getFakeAdminsConfiguration();

        foreach ($config as $adminName => $adminConfig) {
            $adminFactory->create($adminName, $adminConfig);
            $admin = $adminFactory->getAdmin($adminName);
            // test actual admin
            $this->assertInstanceOf('BlueBear\AdminBundle\Admin\Admin', $admin, 'Admin not found after creation');
            $this->assertContains('BlueBear\AdminBundle\Admin\AdminInterface', class_implements(get_class($admin)), 'Admin should implement BlueBear\AdminBundle\Admin\AdminInterface');

            // minimal configuration test
            if ($adminName == 'minimal_entity') {
                $this->doTestMinimalConfiguration($admin);
            } else if ($adminName == 'full_entity') {
                $this->doTestFullConfiguration($admin, $adminConfig);
            }
        }
        $this->assertCount(count($config), $adminFactory->getAdmins(), 'Error on admin count');
    }

    public function testCreateApplicationFromConfiguration()
    {
        $client = $this->initApplication();
        $container = $client->getKernel()->getContainer();
        $adminFactory = new AdminFactory($container);
        // no configuration : test default values
        $application = $adminFactory->createApplicationFromConfiguration([]);
        $this->assertNotNull($application->getTitle());
        $this->assertNotFalse($container->get('templating')->exists($application->getBlockTemplate()));
        $this->assertFalse($application->useBootstrap());
        $this->assertNotNull($application->getDateFormat());
        $this->assertNotNull($application->getDescription());
        $this->assertNotNull($application->getLang());
        $this->assertNotFalse($container->get('templating')->exists($application->getLayout()));
        // test invalid configuration
        $this->assertExceptionRaised('Symfony\Component\OptionsResolver\Exception\UndefinedOptionsException', function () use ($adminFactory) {
            $adminFactory->createApplicationFromConfiguration([
                'new_parameter' => true
            ]);
        });
    }

    public function testGetAdminFromRequest()
    {
        $client = $this->initApplication();
        $container = $client->getKernel()->getContainer();
        $adminFactory = new AdminFactory($container);
        // test invalid route
        $this->assertExceptionRaised('Exception', function () use ($adminFactory) {
            $request = Request::create('/admin/test');
            $adminFactory->getAdminFromRequest($request);
        });
        $this->assertExceptionRaised('Exception', function () use ($adminFactory) {
            $request = Request::create('/admin/test/list', 'GET', [
                '_route_params' => [
                    '_admin' => 'invalid_test',
                    '_action' => 'list',
                ]
            ]);
            $adminFactory->getAdminFromRequest($request);
        });
        // test valid route
        $adminFactory->createApplicationFromConfiguration([]);
        $adminFactory->create('test', [
            'entity' => 'Test\TestBundle\Entity\TestEntity',
            'form' => 'test'
        ]);
        $request = Request::create('/admin/test/list', 'GET', [
            '_route_params' => [
                '_admin' => 'test',
                '_action' => 'list',
            ]
        ]);
        $admin = $adminFactory->getAdminFromRequest($request);
        // test fake admin name
        $this->assertExceptionRaised('Exception', function () use ($adminFactory) {
            $adminFactory->getAdmin('invalid_test');
        });
        $admin->createEntity();
        $this->assertInstanceOf('BlueBear\AdminBundle\Admin\Admin', $admin, 'Admin not found in request');
        $this->assertInstanceOf('BlueBear\AdminBundle\Admin\Admin', $adminFactory->getAdmin('test'), 'Invalid admin');
        $this->assertEquals($admin, $adminFactory->getAdmin('test'), 'Invalid admin');
        $this->assertEquals('test', $admin->getName(), 'Invalid admin name');
        $this->assertEquals('Test\TestBundle\Entity\TestEntity', $admin->getEntityNamespace(), 'Invalid admin namespace');
        $this->assertTrue($admin->getEntity() || count($admin->getEntities()), 'No entities were found');
    }

    protected function getFakeAdminsConfiguration()
    {
        return [
            // minimal configuration sample
            'minimal_entity' => [
                'entity' => 'Test\TestBundle\Entity\TestEntity',
                'form' => 'test'
            ],
            'full_entity' => [
                'entity' => 'Test\TestBundle\Entity\TestEntity',
                'form' => 'test',
                'controller' => 'TestTestBundle:Test',
                'max_per_page' => 50,
                'actions' => [
                    'custom_list' => [
                        'title' => 'My Custom List',
                        'permissions' => [
                            'ROLE_CUSTOM_USER'
                        ],
                        'export' => ['json'],
                        'fields' => [
                            'id' => [],
                            'label' => [
                                'length' => 50
                            ]
                        ],
                        'order' => [
                            'property' => 'createdAt',
                            // TODO test order with fixtures
                            'order' => 'desc'
                        ]
                    ],
                    'custom_edit' => [
                        'title' => 'My Custom Edition',
                        'permissions' => [
                            'ROLE_CUSTOM_EDITOR'
                        ],
                        'export' => ['html'],
                        'fields' => [
                            'id' => [],
                            'label' => []
                        ]
                    ],
                ]
            ]
        ];
    }

    protected function doTestMinimalConfiguration(Admin $admin)
    {
        $this->assertCount(4, $admin->getActions(), 'Invalid actions count, expected 4 (list, create, update, delete)');
        // list default parameters
        $this->assertEquals($admin->getAction('list')->getExport(), ['json', 'xml', 'xls', 'csv', 'html']);
        $this->assertEquals($admin->getAction('list')->getTitle(), 'MinimalEntities List');
        $this->assertEquals(array_keys($admin->getAction('list')->getFields()), ['id']);
        $this->assertContains('ROLE_ADMIN', array_keys($admin->getAction('list')->getPermissions()));
        // create default parameters
        $this->assertEquals($admin->getAction('create')->getExport(), []);
        $this->assertEquals($admin->getAction('create')->getTitle(), 'Create MinimalEntity');
        $this->assertEquals(array_keys($admin->getAction('create')->getFields()), ['id']);
        $this->assertContains('ROLE_ADMIN', array_keys($admin->getAction('list')->getPermissions()));
        // edit default parameters
        $this->assertEquals($admin->getAction('edit')->getExport(), []);
        $this->assertEquals($admin->getAction('edit')->getTitle(), 'Edit MinimalEntity');
        $this->assertEquals(array_keys($admin->getAction('edit')->getFields()), ['id']);
        $this->assertContains('ROLE_ADMIN', array_keys($admin->getAction('list')->getPermissions()));
        // create default parameters
        $this->assertEquals($admin->getAction('delete')->getExport(), []);
        $this->assertEquals($admin->getAction('delete')->getTitle(), 'Delete MinimalEntity');
        $this->assertEquals(array_keys($admin->getAction('delete')->getFields()), ['id']);
        $this->assertContains('ROLE_ADMIN', array_keys($admin->getAction('delete')->getPermissions()));
    }

    protected function doTestFullConfiguration(Admin $admin, array $adminConfiguration)
    {
        $this->assertCount(count($adminConfiguration['actions']), $admin->getActions(), 'Invalid actions count');

        foreach ($adminConfiguration['actions'] as $actionName => $actionConfiguration) {
            // list default parameters
            $this->assertEquals($admin->getAction($actionName)->getExport(), $actionConfiguration['export']);
            $this->assertEquals($admin->getAction($actionName)->getTitle(), $actionConfiguration['title']);
            $this->assertEquals(array_keys($actionConfiguration['fields']), array_keys($admin->getAction($actionName)->getFields()));
            $this->assertEquals($actionConfiguration['permissions'], $admin->getAction($actionName)->getPermissions());
            $this->assertEquals($adminConfiguration['max_per_page'], $admin->getConfiguration()->getMaxPerPage());
            $this->assertEquals($adminConfiguration['controller'], $admin->getController());
        }
    }
}
