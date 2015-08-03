<?php

namespace BlueBear\AdminBundle\Tests;

use BlueBear\AdminBundle\Admin\Admin;
use BlueBear\AdminBundle\Admin\AdminFactory;

class AdminFactoryTest extends Base
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

    public function testCreateAdminFromConfig()
    {
        $client = $this->initApplication();
        $container = $client->getKernel()->getContainer();
        $adminFactory = new AdminFactory($container);
        $config = $this->getFakeAdminsConfiguration();

        foreach ($config as $adminName => $adminConfig) {
            $adminFactory->createAdminFromConfig($adminName, $adminConfig);
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
                            'id' => null,
                            'label' => [
                                'length' => 50
                            ]
                        ]
                    ],
                    'custom_edit' => [
                        'title' => 'My Custom Edition',
                        'permissions' => [
                            'ROLE_CUSTOM_EDITOR'
                        ],
                        'export' => ['html'],
                        'fields' => [
                            'id' => null,
                            'label' => null
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
            $this->assertEquals($adminConfiguration['max_per_page'], $admin->getConfiguration()->maxPerPage);
        }
    }
}
