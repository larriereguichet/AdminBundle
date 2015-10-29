<?php

namespace LAG\AdminBundle\Tests;

use LAG\AdminBundle\Admin\AdminInterface;
use LAG\AdminBundle\Admin\Factory\AdminFactory;
use Symfony\Component\HttpFoundation\Request;

class AdminFactoryFunctionalTest extends Base
{
    public function testInitNoParameters()
    {
        $this->initApplication();
        // admin factory initialization
        $adminFactory = new AdminFactory($this->container);
        // assert factory creation (dependencies are correct...)
        $this->assertTrue($adminFactory != null, 'AdminFactory initialization error');
        // assert that loaded configurations create the right admins count
        $this->assertCount(count($this->container->getParameter('lag.admins')), $adminFactory->getAdmins(), 'No admin should be found');
    }

    public function testCreateAdminFromConfiguration()
    {
        $this->initApplication();
        $adminFactory = new AdminFactory($this->container);
        $config = $this->getFakeAdminsConfiguration();

        foreach ($config as $adminName => $adminConfig) {
            // testing admin creation from configuration
            $adminFactory->create($adminName, $adminConfig);
            // getter should get the right admin
            $admin = $adminFactory->getAdmin($adminName);
            // it should implements AdminInterface
            $this->assertContains('LAG\AdminBundle\Admin\AdminInterface', class_implements(get_class($admin)),
                'Admin should implement LAG\AdminBundle\Admin\AdminInterface');
            $this->doTestAdmin($admin, $adminConfig, $adminName);
        }
        $adminTotalCount = count($config) + count($this->container->getParameter('lag.admins'));
        // assert admin total count is equal to configured admin + admin added dynamically
        $this->assertCount($adminTotalCount, $adminFactory->getAdmins(), 'Error on admin count');
    }

    public function testGetAdminFromRequest()
    {
        $this->initApplication();
        $adminFactory = new AdminFactory($this->container);
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
        $this->assertInstanceOf('LAG\AdminBundle\Admin\Admin', $admin, 'Admin not found in request');
        $this->assertInstanceOf('LAG\AdminBundle\Admin\Admin', $adminFactory->getAdmin('test'), 'Invalid admin');
        $this->assertEquals($admin, $adminFactory->getAdmin('test'), 'Invalid admin');
        $this->assertEquals('test', $admin->getName(), 'Invalid admin name');
        $this->assertEquals('Test\TestBundle\Entity\TestEntity', $admin->getEntityNamespace(), 'Invalid admin namespace');
        $this->assertTrue($admin->getEntity() || count($admin->getEntities()), 'No entities were found');
    }

    protected function doTestAdmin(AdminInterface $admin, array $configuration, $adminName)
    {
        $this->assertEquals($admin->getName(), $adminName);
        $this->assertEquals($admin->getFormType(), $configuration['form']);
        $this->assertEquals($admin->getEntityNamespace(), $configuration['entity']);

        if (array_key_exists('controller', $configuration)) {
            $this->assertEquals($admin->getController(), $configuration['controller']);
        }
        if (array_key_exists('max_per_page', $configuration)) {
            $this->assertEquals($admin->getConfiguration()->getMaxPerPage(), $configuration['max_per_page']);
        } else {
            // value set in Tests/app/config/config.yml
            $this->assertEquals($admin->getConfiguration()->getMaxPerPage(), 25);
        }
        if (array_key_exists('actions', $configuration)) {
            $actionsNames = array_keys($configuration['actions']);
            $this->assertCount(count($actionsNames), $admin->getActions());
            $this->assertEquals(array_keys($admin->getActions()), $actionsNames);
        }
        if (array_key_exists('manager', $configuration)) {
            //$this->assertEquals($admin->getManager(), $configuration['manager']);
        }
    }

    protected function getFakeAdminsConfiguration()
    {
        return [
            // minimal configuration sample
            'minimal_entity' => [
                'entity' => 'Test\TestBundle\Entity\TestEntity',
                'form' => 'test',
                'max_per_page' => 50,
            ],
            'full_entity' => [
                'entity' => 'Test\TestBundle\Entity\TestEntity',
                'form' => 'test',
                'controller' => 'TestTestBundle:Test',
                'max_per_page' => 50,
                'actions' => [
                    'custom_list' => [],
                    'custom_edit' => [],
                ],
                'manager' => 'MyManager'
            ]
        ];
    }
}
