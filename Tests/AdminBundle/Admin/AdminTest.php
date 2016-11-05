<?php

namespace LAG\AdminBundle\Tests\AdminBundle\Admin;

use Doctrine\Common\Collections\ArrayCollection;
use Exception;
use LAG\AdminBundle\Action\Action;
use LAG\AdminBundle\Action\ActionInterface;
use LAG\AdminBundle\Action\Configuration\ActionConfiguration;
use LAG\AdminBundle\Admin\Admin;
use LAG\AdminBundle\Admin\AdminInterface;
use LAG\AdminBundle\Admin\Exception\AdminException;
use LAG\AdminBundle\Filter\RequestFilter;
use LAG\AdminBundle\Tests\AdminTestBase;
use stdClass;
use Symfony\Component\EventDispatcher\EventDispatcher;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Security\Core\Role\Role;
use Symfony\Component\Security\Core\User\User;

class AdminTest extends AdminTestBase
{
    /**
     * Test if configuration is properly set.
     */
    public function testAdmin()
    {
        $configurations = $this->getFakeAdminsConfiguration();

        foreach ($configurations as $adminName => $configuration) {
            $admin = $this->createAdmin($adminName, $configuration);
            $this->doTestAdmin($admin, $configuration, $adminName);
        }
    }

    /**
     * handleRequest method SHOULD throw an exception if the action is not valid.
     */
    public function testHandleRequest()
    {
        $configurations = $this->getFakeAdminsConfiguration();
        $applicationConfiguration = $this->createApplicationConfiguration();

        foreach ($configurations as $adminName => $configuration) {

            $admin = $this->createAdmin($adminName, $configuration);
            $this->doTestAdmin($admin, $configuration, $adminName);

            // with no action, handleRequest method SHOULD throw an exception
            $this->assertExceptionRaised('Exception', function () use ($admin) {
                $request = new Request();
                $admin->handleRequest($request);
            });

            // with a wrong action, handleRequest method SHOULD throw an exception
            $this->assertExceptionRaised('Exception', function () use ($admin) {
                $request = new Request([], [], [
                    '_route_params' => [
                        '_action' => 'bad_action'
                    ]
                ]);
                $admin->handleRequest($request);
            });

            // with an existing action, handleRequest method SHOULD NOT throwing an exception
            $request = new Request([], [], [
                '_route_params' => [
                    '_action' => 'custom_list'
                ]
            ]);
            $admin->handleRequest($request);
        }

        // test pagerfanta filter
        $configurations = $this->getFakeAdminsConfiguration();
        $configuration = $configurations['full_entity'];
        $admin = $this->createAdmin('full_entity', $configuration);

        $actionConfiguration = $this->createActionConfiguration('custom_list', $admin, [
            'load_strategy' => AdminInterface::LOAD_STRATEGY_UNIQUE,
            'route' => '',
            'export' => '',
            'order' => [],
            'icon' => '',
            'pager' => 'pagerfanta',
            'criteria' => [],
        ]);

        $admin->addAction(new Action('custom_list', $actionConfiguration));
        $this->assertTrue($admin->hasAction('custom_list'));
        $this->assertTrue($admin->isActionGranted('custom_list', [
            'ROLE_ADMIN',
            new Role('ROLE_ADMIN')
        ]));
        $this->assertFalse($admin->isActionGranted('custom_list', [
            'WRONG_ROLE',
            'IS_AUTHENTICATED_ANONYMOUSLY'
        ]));


        $request = new Request([], [], [
            '_route_params' => [
                '_action' => 'custom_list'
            ]
        ]);
        $admin->handleRequest($request);

        // test load strategy none
        $configurations = $this->getFakeAdminsConfiguration();
        $configuration = $configurations['full_entity'];

        $admin = $this->createAdmin('full_entity', $configuration, $applicationConfiguration->getParameters());

        $admin->addAction($this->createAction('custom_list', $admin, [
            'load_strategy' => Admin::LOAD_STRATEGY_NONE,
            'route' => '',
            'export' => '',
            'order' => [],
            'icon' => '',
            'pager' => 'pagerfanta',
            'criteria' => [],
        ]));
        $request = new Request([], [], [
            '_route_params' => [
                '_action' => 'custom_list'
            ]
        ]);
        $admin->handleRequest($request);
    }

    /**
     * checkPermissions method SHOULD throw an exception if the permissions are invalid.
     */
    public function testCheckPermissions()
    {
        $configurations = $this->getFakeAdminsConfiguration();

        foreach ($configurations as $adminName => $configuration) {
            $admin = $this->createAdmin($adminName, $configuration);
            $this->doTestAdmin($admin, $configuration, $adminName);

            // with a current action unset, checkPermissions method SHOULD throw an exception
            $this->assertExceptionRaised('Exception', function () use ($admin) {
                $user = new User('JohnKrovitch', 'john1234');
                $admin->checkPermissions($user);
            });

            // with the wrong roles, checkPermissions method SHOULD throw an exception
            $this->assertExceptionRaised(NotFoundHttpException::class, function () use ($admin) {
                $request = new Request([], [], [
                    '_route_params' => [
                        '_action' => 'custom_list'
                    ]
                ]);
                $user = new User('JohnKrovitch', 'john1234');
                $admin->handleRequest($request);
                $admin->checkPermissions($user);
            });

            // with the wrong roles, checkPermissions method SHOULD throw an exception
            $this->assertExceptionRaised(NotFoundHttpException::class, function () use ($admin) {
                $request = new Request([], [], [
                    '_route_params' => [
                        '_action' => 'custom_list'
                    ]
                ]);
                $user = new User('JohnKrovitch', 'john1234', [
                    'ROLE_USER',
                    new Role('ROLE_USER')
                ]);
                $admin->handleRequest($request);
                $admin->checkPermissions($user);
            });

            // with the right role, checkPermissions method SHOULD NOT throw an exception
            $request = new Request([], [], [
                '_route_params' => [
                    '_action' => 'custom_list'
                ]
            ]);
            $user = new User('JohnKrovitch', 'john1234', [
                'ROLE_ADMIN'
            ]);
            $admin->handleRequest($request);
            $admin->checkPermissions($user);
        }
    }

    /**
     * create method SHOULD call the create method in the data provider.
     */
    public function testCreate()
    {
        $dataProvider = $this->mockDataProvider();
        $dataProvider
            ->expects($this->once())
            ->method('create');
        $applicationConfiguration = $this->createApplicationConfiguration();
        $adminConfiguration = $this->createAdminConfiguration($applicationConfiguration, $this->getFakeAdminsConfiguration()['full_entity']);

        $admin = new Admin(
            'test',
            $dataProvider,
            $adminConfiguration,
            $this->mockMessageHandler(),
            new EventDispatcher(),
            new RequestFilter()
        );
        $admin->create();
    }

    /**
     * save method SHOULD call the save method in the data provider.
     */
    public function testSave()
    {
        $dataProvider = $this->mockDataProvider();
        $dataProvider
            ->expects($this->once())
            ->method('save');
        $dataProvider
            ->method('create')
            ->willReturn(new stdClass());
        $applicationConfiguration = $this->createApplicationConfiguration();
        $adminConfiguration = $this
            ->createAdminConfiguration($applicationConfiguration, $this->getFakeAdminsConfiguration()['full_entity']);

        $admin = new Admin(
            'test',
            $dataProvider,
            $adminConfiguration,
            $this->mockMessageHandler(),
            new EventDispatcher(),
            new RequestFilter()
        );
        $admin->create();
        $this->assertTrue($admin->save());
    }

    /**
     * save method SHOULD return false if an exception is thrown. This exception should be catch.
     */
    public function testSaveWithException()
    {
        $adminConfiguration = $this
            ->createAdminConfiguration(
                $this->createApplicationConfiguration(),
                $this->getFakeAdminsConfiguration()['full_entity']
            );
        $dataProvider = $this->mockDataProvider();
        $dataProvider
            ->expects($this->once())
            ->method('save')
            ->willThrowException(new Exception());
        $dataProvider
            ->method('create')
            ->willReturn(new stdClass());

        $admin = new Admin(
            'test',
            $dataProvider,
            $adminConfiguration,
            $this->mockMessageHandler(),
            new EventDispatcher(),
            new RequestFilter()
        );
        $admin->create();
        $this->assertFalse($admin->save());
    }

    /**
     * remove method SHOULD call the remove method in the data provider.
     */
    public function testRemove()
    {
        $dataProvider = $this->mockDataProvider();
        $dataProvider
            ->expects($this->once())
            ->method('remove');
        $dataProvider
            ->method('create')
            ->willReturn(new stdClass());
        $applicationConfiguration = $this->createApplicationConfiguration();
        $adminConfiguration = $this->createAdminConfiguration($applicationConfiguration, $this->getFakeAdminsConfiguration()['full_entity']);

        $admin = new Admin(
            'test',
            $dataProvider,
            $adminConfiguration,
            $this->mockMessageHandler(),
            new EventDispatcher(),
            new RequestFilter()
        );
        $admin->create();
        $this->assertTrue($admin->remove());
    }

    /**
     * save method SHOULD return false if an exception is thrown. This exception should be catch.
     */
    public function testRemoveWithException()
    {
        $adminConfiguration = $this->createAdminConfiguration(
            $this->createApplicationConfiguration(),
            $this->getFakeAdminsConfiguration()['full_entity']
        );
        $dataProvider = $this->mockDataProvider();
        $dataProvider
            ->expects($this->once())
            ->method('remove')
            ->willThrowException(new Exception());
        $dataProvider
            ->method('create')
            ->willReturn(new stdClass());

        $admin = new Admin(
            'test',
            $dataProvider,
            $adminConfiguration,
            $this->mockMessageHandler(),
            new EventDispatcher(),
            new RequestFilter()
        );
        $admin->create();
        $this->assertFalse($admin->remove());
    }

    /**
     * generateRouteName SHOULD use the configured pattern to generate route name. It SHOULD throw an exception if the
     * given action name does not exists.
     *
     * @throws Exception
     */
    public function testGenerateRouteName()
    {
        $testEntities = [
            new stdClass(),
            new stdClass(),
        ];
        $dataProvider = $this->mockDataProvider();
        $dataProvider
            ->method('findBy')
            ->willReturn($testEntities);

        $applicationConfiguration = $this->createApplicationConfiguration();
        $adminConfiguration = $this->createAdminConfiguration($applicationConfiguration, $this->getFakeAdminsConfiguration()['full_entity']);

        $admin = new Admin(
            'test',
            $dataProvider,
            $adminConfiguration,
            $this->mockMessageHandler(),
            new EventDispatcher(),
            new RequestFilter()
        );
        $this->assertEquals('lag.test.custom_list', $admin->generateRouteName('custom_list'));
        $this->assertEquals('lag.test.custom_edit', $admin->generateRouteName('custom_edit'));
        $this->assertExceptionRaised(Exception::class, function () use ($admin) {
            $admin->generateRouteName('wrong_action_name');
        });
    }

    /**
     * getEntities method SHOULD return the entities passed by the data provider.
     */
    public function testGetEntities()
    {
        $testEntities = [
            new stdClass(),
            new stdClass(),
        ];
        $dataProvider = $this->mockDataProvider($testEntities);
        $applicationConfiguration = $this->createApplicationConfiguration();
        $adminConfiguration = $this->createAdminConfiguration($applicationConfiguration, $this->getFakeAdminsConfiguration()['full_entity']);

        $admin = new Admin(
            'test',
            $dataProvider,
            $adminConfiguration,
            $this->mockMessageHandler(),
            new EventDispatcher(),
            new RequestFilter()
        );
        $request = new Request([], [], [
            '_route_params' => [
                '_action' => 'custom_list'
            ]
        ]);
        $admin->addAction($this->createAction('custom_list', $admin, [
            'load_strategy' => AdminInterface::LOAD_STRATEGY_UNIQUE,
            'route' => '',
            'export' => '',
            'order' => [],
            'icon' => '',
            'pager' => 'pagerfanta',
            'criteria' => [],
        ]));

        $admin->handleRequest($request);
        $admin->load([]);

        // getEntities method SHOULD return the entities passed by the data provider.
        $this->assertEquals($testEntities, $admin->getEntities()->toArray());
    }

    /**
     * getUniqueEntity method SHOULD return the entity passed by the data provider.
     */
    public function testGetUniqueEntity()
    {
        $testEntity = new stdClass();
        $dataProvider = $this->mockDataProvider([$testEntity]);

        $applicationConfiguration = $this->createApplicationConfiguration();
        $adminConfiguration = $this->createAdminConfiguration($applicationConfiguration, $this->getFakeAdminsConfiguration()['full_entity']);

        $admin = new Admin(
            'test',
            $dataProvider,
            $adminConfiguration,
            $this->mockMessageHandler(),
            new EventDispatcher(),
            new RequestFilter()
        );
        $request = new Request([], [], [
            '_route_params' => [
                '_action' => 'custom_list'
            ]
        ]);
        $admin->addAction($this->createAction('custom_list', $admin, [
            'load_strategy' => AdminInterface::LOAD_STRATEGY_UNIQUE,
            'route' => '',
            'export' => '',
            'order' => [],
            'icon' => '',
            'pager' => 'pagerfanta',
            'criteria' => [],
        ]));
        $admin->handleRequest($request);
        $admin->load([]);

        // getEntities method SHOULD return the entities passed by the data provider.
        $this->assertEquals($testEntity, $admin->getUniqueEntity());

        $testEntity = new stdClass();
        $dataProvider = $this->mockDataProvider([$testEntity, $testEntity]);

        $admin = new Admin(
            'test',
            $dataProvider,
            $adminConfiguration,
            $this->mockMessageHandler(),
            new EventDispatcher(),
            new RequestFilter()
        );
        $request = new Request([], [], [
            '_route_params' => [
                '_action' => 'custom_list'
            ]
        ]);
        $admin->addAction($this->createAction('custom_list', $admin, [
            'load_strategy' => AdminInterface::LOAD_STRATEGY_UNIQUE,
            'route' => '',
            'export' => '',
            'order' => [],
            'icon' => '',
            'pager' => 'pagerfanta',
            'criteria' => [],
        ]));
        $this->assertExceptionRaised(Exception::class, function () use ($admin, $request, $testEntity) {
            $admin->handleRequest($request);
            $admin->load([]);
            // getEntities method SHOULD return the entities passed by the data provider.
            $this->assertEquals($testEntity, $admin->getUniqueEntity());
        });

        $testEntity = new stdClass();
        $dataProvider = $this->mockDataProvider([]);

        $admin = new Admin(
            'test',
            $dataProvider,
            $adminConfiguration,
            $this->mockMessageHandler(),
            new EventDispatcher(),
            new RequestFilter()
        );
        $request = new Request([], [], [
            '_route_params' => [
                '_action' => 'custom_list'
            ]
        ]);
        $admin->addAction($this->createAction('custom_list', $admin, [
            'load_strategy' => AdminInterface::LOAD_STRATEGY_UNIQUE,
            'route' => '',
            'export' => '',
            'order' => [],
            'icon' => '',
            'pager' => 'pagerfanta',
            'criteria' => [],
        ]));
        $this->assertExceptionRaised(Exception::class, function () use ($admin, $request, $testEntity) {
            $admin->handleRequest($request);
            $admin->load([]);
            // getEntities method SHOULD return the entities passed by the data provider.
            $this->assertEquals($testEntity, $admin->getUniqueEntity());
        });
    }

    /**
     * load method SHOULD load entities from data provider to the admin.
     */
    public function testLoad()
    {
        $testEntities = [
            new stdClass(),
            new stdClass(),
        ];
        $dataProvider = $this->mockDataProvider($testEntities);

        $adminConfiguration = $this
            ->createAdminConfiguration(
                $this->createApplicationConfiguration(),
                $this->getFakeAdminsConfiguration()['full_entity']
            );

        $admin = new Admin(
            'test',
            $dataProvider,
            $adminConfiguration,
            $this->mockMessageHandler(),
            new EventDispatcher(),
            new RequestFilter()
        );
        $request = new Request([], [], [
            '_route_params' => [
                '_action' => 'custom_list'
            ]
        ]);
        $admin->addAction($this->createAction('custom_list', $admin, [
            'load_strategy' => AdminInterface::LOAD_STRATEGY_UNIQUE,
            'route' => '',
            'export' => '',
            'order' => [],
            'icon' => '',
            'pager' => 'pagerfanta',
            'criteria' => [],
        ]));
        $admin->handleRequest($request);
        $admin->load([]);

        // if an array is returned from the data provider, it SHOULD wrapped into an array collection
        $this->assertEquals(new ArrayCollection($testEntities), $admin->getEntities());
    }

    /**
     * Load method should not load entities if the strategy is NONE.
     */
    public function testLoadWithoutRequireLoad()
    {
        $testEntities = [
            new stdClass(),
            new stdClass(),
        ];
        $dataProvider = $this->mockDataProvider($testEntities);

        $adminConfiguration = $this
            ->createAdminConfiguration(
                $this->createApplicationConfiguration(),
                $this->getFakeAdminsConfiguration()['full_entity']
            );

        $admin = new Admin(
            'test',
            $dataProvider,
            $adminConfiguration,
            $this->mockMessageHandler(),
            new EventDispatcher(),
            new RequestFilter()
        );
        $request = new Request([], [], [
            '_route_params' => [
                '_action' => 'custom_list'
            ]
        ]);
        $admin->addAction($this->createAction('custom_list', $admin, [
            'load_strategy' => AdminInterface::LOAD_STRATEGY_NONE,
            'route' => '',
            'export' => '',
            'order' => [],
            'icon' => '',
            'pager' => 'pagerfanta',
            'criteria' => [],
        ]));
        $admin->handleRequest($request);
        $admin->load([]);

        // if an array is returned from the data provider, it SHOULD wrapped into an array collection
        $this->assertCount(0, $admin->getEntities());
    }

    /**
     * load method with unique strategy SHOULD run successfully.
     */
    public function testLoadWithUniqueStrategy()
    {
        $adminConfiguration = $this
            ->createAdminConfiguration(
                $this->createApplicationConfiguration(),
                $this->getFakeAdminsConfiguration()['full_entity']
            );
        $dataProvider = $this->mockDataProvider([
            new stdClass(),
            new stdClass(),
        ]);

        $admin = new Admin(
            'test',
            $dataProvider,
            $adminConfiguration,
            $this->mockMessageHandler(),
            new EventDispatcher(),
            new RequestFilter()
        );
        $request = new Request([], [], [
            '_route_params' => [
                '_action' => 'custom_list'
            ]
        ]);
        $admin->addAction($this->createAction('custom_list', $admin, [
            'load_strategy' => AdminInterface::LOAD_STRATEGY_UNIQUE,
            'route' => '',
            'export' => '',
            'order' => [],
            'icon' => '',
            'pager' => 'pagerfanta',
            'criteria' => [],
        ]));
        $admin->handleRequest($request);
        $admin->load([]);
    }

    /**
     * load method with multiple strategy SHOULD run successfully.
     */
    public function testLoadWithPagerWithMultipleStrategy()
    {
        $adminConfiguration = $this
            ->createAdminConfiguration(
                $this->createApplicationConfiguration(),
                $this->getFakeAdminsConfiguration()['full_entity']
            );
        $dataProvider = $this->mockDataProvider([
            new stdClass(),
            new stdClass(),
        ]);
        $admin = new Admin(
            'test',
            $dataProvider,
            $adminConfiguration,
            $this->mockMessageHandler(),
            new EventDispatcher(),
            new RequestFilter()
        );
        $request = new Request([], [], [
            '_route_params' => [
                '_action' => 'custom_list'
            ]
        ]);
        $admin->addAction($this->createAction('custom_list', $admin, [
            'load_strategy' => AdminInterface::LOAD_STRATEGY_MULTIPLE,
            'route' => '',
            'export' => '',
            'order' => [],
            'icon' => '',
            'pager' => 'pagerfanta',
            'criteria' => [],
        ]));
        $admin->handleRequest($request);
        $admin->load([]);
    }

    /**
     * load method with a wrong pager should throw an exception.
     */
    public function testLoadWithWrongPager()
    {
        $adminConfiguration = $this
            ->createAdminConfiguration(
                $this->createApplicationConfiguration(),
                $this->getFakeAdminsConfiguration()['full_entity']
            );
        $dataProvider = $this->mockDataProvider([
            new stdClass(),
            new stdClass(),
        ]);
        $admin = new Admin(
            'test',
            $dataProvider,
            $adminConfiguration,
            $this->mockMessageHandler(),
            new EventDispatcher(),
            new RequestFilter()
        );
        $request = new Request([], [], [
            '_route_params' => [
                '_action' => 'custom_list'
            ]
        ]);
        $actionConfiguration = new ActionConfiguration('custom_list', $admin);
        $actionConfiguration->setParameters([
            'criteria' => [],
            'order' => [],
            'pager' => 'wrong',
            'load_strategy' => AdminInterface::LOAD_STRATEGY_MULTIPLE
        ]);
        $action = $this->createMock(ActionInterface::class);
        $action
            ->method('getName')
            ->willReturn('custom_list');
        $action
            ->method('getConfiguration')
            ->willReturn($actionConfiguration);
        $action
            ->method('isPaginationRequired')
            ->willReturn(true);
        $action
            ->method('isLoadingRequired')
            ->willReturn(true);

        $admin->addAction($action);

        $this->assertExceptionRaised(AdminException::class, function () use ($admin, $request) {
            $admin->handleRequest($request);
        });
    }

    /**
     * load method with a wrong pager should throw an exception.
     */
    public function testLoadWithPagerAndWrongStrategy()
    {
        $adminConfiguration = $this
            ->createAdminConfiguration(
                $this->createApplicationConfiguration(),
                $this->getFakeAdminsConfiguration()['full_entity']
            );
        $dataProvider = $this->mockDataProvider([
            new stdClass(),
            new stdClass(),
        ]);
        $admin = new Admin(
            'test',
            $dataProvider,
            $adminConfiguration,
            $this->mockMessageHandler(),
            new EventDispatcher(),
            new RequestFilter()
        );
        $request = new Request([], [], [
            '_route_params' => [
                '_action' => 'custom_list'
            ]
        ]);
        $actionConfiguration = new ActionConfiguration('custom_list', $admin);
        $actionConfiguration->setParameters([
            'criteria' => [],
            'order' => [],
            'pager' => 'pagerfanta',
            'load_strategy' => AdminInterface::LOAD_STRATEGY_UNIQUE
        ]);
        $action = $this->createMock(ActionInterface::class);
        $action
            ->method('getName')
            ->willReturn('custom_list');
        $action
            ->method('getConfiguration')
            ->willReturn($actionConfiguration);
        $action
            ->method('isPaginationRequired')
            ->willReturn(true);
        $action
            ->method('isLoadingRequired')
            ->willReturn(true);

        $admin->addAction($action);

        $this->assertExceptionRaised(AdminException::class, function () use ($admin, $request) {
            $admin->handleRequest($request);
        });
    }

    /**
     * load method SHOULD handle exceptions.
     */
    public function testLoadWithException()
    {
        $dataProvider = $this->mockDataProvider(new stdClass());
        $adminConfiguration = $this
            ->createAdminConfiguration(
                $this->createApplicationConfiguration(),
                $this->getFakeAdminsConfiguration()['full_entity']
            );

        $admin = new Admin(
            'test',
            $dataProvider,
            $adminConfiguration,
            $this->mockMessageHandler(),
            new EventDispatcher(),
            new RequestFilter()
        );
        $request = new Request([], [], [
            '_route_params' => [
                '_action' => 'custom_list'
            ]
        ]);
        $admin->addAction($this->createAction('custom_list', $admin, [
            'load_strategy' => AdminInterface::LOAD_STRATEGY_UNIQUE,
            'route' => '',
            'export' => '',
            'order' => [],
            'icon' => '',
            'pager' => 'pagerfanta',
            'criteria' => [],
        ]));
        $this->assertExceptionRaised(AdminException::class, function () use ($admin, $request) {
            $admin->handleRequest($request);
            $admin->load([]);
        });
    }

    /**
     * load method SHOULD work without a pager.
     */
    public function testLoadWithoutPager()
    {
        $testEntities = [
            new stdClass(),
            new stdClass(),
        ];
        $dataProvider = $this->mockDataProvider($testEntities);

        $applicationConfiguration = $this->createApplicationConfiguration();
        $adminConfiguration = $this->createAdminConfiguration($applicationConfiguration, $this->getFakeAdminsConfiguration()['full_entity']);

        $admin = new Admin(
            'test',
            $dataProvider,
            $adminConfiguration,
            $this->mockMessageHandler(),
            new EventDispatcher(),
            new RequestFilter()
        );
        $request = new Request([], [], [
            '_route_params' => [
                '_action' => 'custom_list'
            ]
        ]);
        $admin->addAction($this->createAction('custom_list', $admin, [
            'load_strategy' => AdminInterface::LOAD_STRATEGY_MULTIPLE,
            'route' => '',
            'export' => '',
            'order' => [],
            'icon' => '',
            'pager' => false,
            'criteria' => [],
        ]));
        $admin->handleRequest($request);
        $admin->load([]);
    }

    /**
     * getCurrentAction method SHOULD throw an exception if no pager is configured.
     */
    public function testGetCurrentActionException()
    {
        $testEntities = [
            new stdClass(),
            new stdClass(),
        ];
        $dataProvider = $this->mockDataProvider($testEntities);

        $applicationConfiguration = $this->createApplicationConfiguration();
        $adminConfiguration = $this->createAdminConfiguration($applicationConfiguration, $this->getFakeAdminsConfiguration()['full_entity']);

        $admin = new Admin(
            'test',
            $dataProvider,
            $adminConfiguration,
            $this->mockMessageHandler(),
            new EventDispatcher(),
            new RequestFilter()
        );
        $admin->addAction($this->createAction('custom_list', $admin, [
            'load_strategy' => AdminInterface::LOAD_STRATEGY_MULTIPLE,
            'route' => '',
            'export' => '',
            'order' => [],
            'icon' => '',
            'pager' => false,
            'criteria' => [],
        ]));

        $this->assertExceptionRaised(Exception::class, function () use ($admin) {
            $admin->getCurrentAction();
        });
        $this->assertFalse($admin->isCurrentActionDefined());
    }

    protected function doTestAdmin(AdminInterface $admin, array $configuration, $adminName)
    {
        $this->assertEquals($admin->getName(), $adminName);
        $this->assertEquals($admin->getConfiguration()->getParameter('form'), $configuration['form']);
        $this->assertEquals($admin->getConfiguration()->getParameter('entity'), $configuration['entity']);

        if (array_key_exists('controller', $configuration)) {
            $this->assertEquals($admin->getConfiguration()->getParameter('controller'), $configuration['controller']);
        }
        if (array_key_exists('max_per_page', $configuration)) {
            $this->assertEquals($admin->getConfiguration()->getParameter('max_per_page'), $configuration['max_per_page']);
        } else {
            $this->assertEquals($admin->getConfiguration()->getParameter('max_per_page'), 25);
        }

        if (!array_key_exists('actions', $configuration)) {
            $configuration['actions'] = [
                'create' => [],
                'edit' => [],
                'delete' => [],
                'list' => []
            ];
        }

        foreach ($configuration['actions'] as $actionName => $actionConfiguration) {
            $action = $this->createAction($actionName, $admin);
            $admin->addAction($action);
        }
        $expectedActionNames = array_keys($configuration['actions']);
        $this->assertEquals($expectedActionNames, array_keys($admin->getActions()));
    }

    /**
     * @return array
     */
    protected function getFakeAdminsConfiguration()
    {
        return [
            'full_entity' => [
                'entity' => 'Test\TestBundle\Entity\TestEntity',
                'form' => 'test',
                'controller' => 'TestTestBundle:Test',
                'max_per_page' => 50,
                'actions' => [
                    'custom_list' => [],
                    'custom_edit' => [],
                ],
                'routing_url_pattern' => 'lag.admin.{admin}',
                'routing_name_pattern' => 'lag.{admin}.{action}',
                'data_provider' => null,
            ]
        ];
    }
}
