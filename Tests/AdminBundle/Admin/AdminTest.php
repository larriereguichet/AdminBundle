<?php

namespace LAG\AdminBundle\Tests\AdminBundle\Admin;

use Doctrine\ORM\Mapping\ClassMetadata;
use Exception;
use LAG\AdminBundle\Admin\Action;
use LAG\AdminBundle\Admin\Admin;
use LAG\AdminBundle\Admin\AdminInterface;
use LAG\AdminBundle\Admin\Configuration\ActionConfiguration;
use LAG\AdminBundle\Admin\Configuration\AdminConfiguration;
use LAG\AdminBundle\Tests\Base;
use stdClass;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Security\Core\User\User;

class AdminTest extends Base
{
    /**
     * Test if configuration is properly set.
     */
    public function testAdmin()
    {
        $configurations = $this->getFakeAdminsConfiguration();

        foreach ($configurations as $adminName => $configuration) {
            $adminConfiguration = new AdminConfiguration($configuration);
            $admin = $this->mockAdmin($adminName, $adminConfiguration);
            $this->doTestAdmin($admin, $configuration, $adminName);
        }
    }

    /**
     * handleRequest method SHOULD throw an exception if the action is not valid.
     * handleRequest method SHOULD throw an exception if the action is not valid.
     */
    public function testHandleRequest()
    {
        $configurations = $this->getFakeAdminsConfiguration();

        foreach ($configurations as $adminName => $configuration) {
            $adminConfiguration = new AdminConfiguration($configuration);
            $admin = $this->mockAdmin($adminName, $adminConfiguration);
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

        $adminConfiguration = new AdminConfiguration($configuration);
        $admin = $this->mockAdmin('full_entity', $adminConfiguration);

        $admin->addAction(new Action('custom_list', [
            'title' => 'Test action',
            'permissions' => [
                'ROLE_ADMIN'
            ],
            'submit_actions' => [],
            'batch' => [],
        ], new ActionConfiguration([
                'load_strategy' => '',
                'route' => '',
                'parameters' => '',
                'export' => '',
                'order' => '',
                'target' => '',
                'icon' => '',
                'batch' => '',
                'pager' => 'pagerfanta',
                'criteria' => [],
            ])
        ));
        $request = new Request([], [], [
            '_route_params' => [
                '_action' => 'custom_list'
            ]
        ]);
        $admin->handleRequest($request);

        // test load stregy none
        $configurations = $this->getFakeAdminsConfiguration();
        $configuration = $configurations['full_entity'];

        $adminConfiguration = new AdminConfiguration($configuration);
        $admin = $this->mockAdmin('full_entity', $adminConfiguration);

        $admin->addAction(new Action('custom_list', [
            'title' => 'Test action',
            'permissions' => [
                'ROLE_ADMIN'
            ],
            'submit_actions' => [],
            'batch' => [],
        ], new ActionConfiguration([
                'load_strategy' => Admin::LOAD_STRATEGY_NONE,
                'route' => '',
                'parameters' => '',
                'export' => '',
                'order' => '',
                'target' => '',
                'icon' => '',
                'batch' => '',
                'pager' => 'pagerfanta',
                'criteria' => [],
            ])
        ));
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
            $adminConfiguration = new AdminConfiguration($configuration);
            $admin = $this->mockAdmin($adminName, $adminConfiguration);
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
            ->method('create')
        ;

        $admin = new Admin(
            'test',
            $dataProvider,
            new AdminConfiguration($this->getFakeAdminsConfiguration()['full_entity']),
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
            ->method('save')
        ;
        $dataProvider
            ->method('create')
            ->willReturn(new stdClass())
        ;

        $admin = new Admin(
            'test',
            $dataProvider,
            new AdminConfiguration($this->getFakeAdminsConfiguration()['full_entity']),
            $this->mockMessageHandler()
        );
        $admin->create();
        $this->assertTrue($admin->save());

        // test exception
        $dataProvider = $this->mockDataProvider();
        $dataProvider
            ->expects($this->once())
            ->method('save')
            ->willThrowException(new Exception())
        ;
        $dataProvider
            ->method('create')
            ->willReturn(new stdClass())
        ;

        $admin = new Admin(
            'test',
            $dataProvider,
            new AdminConfiguration($this->getFakeAdminsConfiguration()['full_entity']),
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
            ->method('remove')
        ;
        $dataProvider
            ->method('create')
            ->willReturn(new stdClass())
        ;

        $admin = new Admin(
            'test',
            $dataProvider,
            new AdminConfiguration($this->getFakeAdminsConfiguration()['full_entity']),
            $this->mockMessageHandler()
        );
        $admin->create();
        $this->assertTrue($admin->remove());

        // test exception
        $dataProvider = $this->mockDataProvider();
        $dataProvider
            ->expects($this->once())
            ->method('remove')
            ->willThrowException(new Exception())
        ;
        $dataProvider
            ->method('create')
            ->willReturn(new stdClass())
        ;

        $admin = new Admin(
            'test',
            $dataProvider,
            new AdminConfiguration($this->getFakeAdminsConfiguration()['full_entity']),
            $this->mockMessageHandler()
        );
        $admin->create();
        $this->assertFalse($admin->remove());
    }

    protected function doTestAdmin(AdminInterface $admin, array $configuration, $adminName)
    {
        $this->assertEquals($admin->getName(), $adminName);
        $this->assertEquals($admin->getConfiguration()->getFormType(), $configuration['form']);
        $this->assertEquals($admin->getConfiguration()->getEntityName(), $configuration['entity']);

        if (array_key_exists('controller', $configuration)) {
            $this->assertEquals($admin->getConfiguration()->getControllerName(), $configuration['controller']);
        }
        if (array_key_exists('max_per_page', $configuration)) {
            $this->assertEquals($admin->getConfiguration()->getMaxPerPage(), $configuration['max_per_page']);
        } else {
            $this->assertEquals($admin->getConfiguration()->getMaxPerPage(), 25);
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
            $action = $this->mockAction($actionName);
            $admin->addAction($action);
        }
        $expectedActionNames = array_keys($configuration['actions']);
        $this->assertEquals($expectedActionNames, array_keys($admin->getActions()));
    }

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
                'manager' => 'Test\TestBundle\Manager\TestManager',
                'routing_url_pattern' => 'lag.admin.{admin}',
                'routing_name_pattern' => 'lag.{admin}.{action}',
                'data_provider' => null,
                'metadata' => new ClassMetadata('LAG\AdminBundle\Tests\Entity\EntityTest'),
            ]
        ];
    }
}
