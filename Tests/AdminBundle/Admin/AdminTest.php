<?php

namespace LAG\AdminBundle\Tests\AdminBundle\Admin;

use Doctrine\Common\Collections\ArrayCollection;
use Exception;
use LAG\AdminBundle\Action\Action;
use LAG\AdminBundle\Admin\Admin;
use LAG\AdminBundle\Admin\AdminInterface;
use LAG\AdminBundle\Tests\AdminTestBase;
use stdClass;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
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


        $request = new Request([], [], [
            '_route_params' => [
                '_action' => 'custom_list'
            ]
        ]);
        $admin->handleRequest($request);

        // test load strategy none
        $configurations = $this->getFakeAdminsConfiguration();
        $configuration = $configurations['full_entity'];

        $adminConfiguration = $this->createAdminConfiguration($applicationConfiguration, $configuration);
        $admin = $this->mockAdmin('full_entity', $adminConfiguration);

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
     * checkPermissions method SHOULd throw an exception if the permissions are invalid.
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
                    'ROLE_USER'
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
            $this->mockMessageHandler()
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
        $adminConfiguration = $this->createAdminConfiguration($applicationConfiguration, $this->getFakeAdminsConfiguration()['full_entity']);

        $admin = new Admin(
            'test',
            $dataProvider,
            $adminConfiguration,
            $this->mockMessageHandler()
        );
        $admin->create();
        $this->assertTrue($admin->save());

        // test exception
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
            $this->mockMessageHandler()
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
            $this->mockMessageHandler()
        );
        $admin->create();
        $this->assertTrue($admin->remove());

        // test exception
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
            $this->mockMessageHandler()
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
            $this->mockMessageHandler()
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
            $this->mockMessageHandler()
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
            $this->mockMessageHandler()
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
            $this->mockMessageHandler()
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
            $this->mockMessageHandler()
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

        $applicationConfiguration = $this->createApplicationConfiguration();
        $adminConfiguration = $this->createAdminConfiguration($applicationConfiguration, $this->getFakeAdminsConfiguration()['full_entity']);

        $admin = new Admin(
            'test',
            $dataProvider,
            $adminConfiguration,
            $this->mockMessageHandler()
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

        $dataProvider = $this->mockDataProvider($testEntities);

        $admin = new Admin(
            'test',
            $dataProvider,
            $adminConfiguration,
            $this->mockMessageHandler()
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

        // test exception
        $dataProvider = $this->mockDataProvider(new stdClass());

        $admin = new Admin(
            'test',
            $dataProvider,
            $adminConfiguration,
            $this->mockMessageHandler()
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
        $this->assertExceptionRaised(Exception::class, function () use ($admin, $request) {
            $admin->handleRequest($request);
            $admin->load([]);
        });
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
