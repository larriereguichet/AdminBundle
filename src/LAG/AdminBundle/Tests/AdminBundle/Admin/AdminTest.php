<?php

namespace LAG\AdminBundle\Tests\AdminBundle\Admin;

use Doctrine\Common\Collections\ArrayCollection;
use LAG\AdminBundle\Action\ActionInterface;
use LAG\AdminBundle\Action\Configuration\ActionConfiguration;
use LAG\AdminBundle\Admin\Admin;
use LAG\AdminBundle\Admin\Configuration\AdminConfiguration;
use LAG\AdminBundle\Admin\Request\RequestHandlerInterface;
use LAG\AdminBundle\DataProvider\DataProviderInterface;
use LAG\AdminBundle\DataProvider\Loader\EntityLoaderInterface;
use LAG\AdminBundle\Message\MessageHandlerInterface;
use LAG\AdminBundle\Tests\AdminTestBase;
use LAG\AdminBundle\Tests\Entity\TestSimpleEntity;
use LAG\AdminBundle\View\Factory\ViewFactory;
use LAG\AdminBundle\View\ViewInterface;
use LogicException;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use Symfony\Component\Security\Core\User\UserInterface;

class AdminTest extends AdminTestBase
{
    public function testHandleRequest()
    {
        $configuration = $this->getMockWithoutConstructor(AdminConfiguration::class);
        $messageHandler = $this->getMockWithoutConstructor(MessageHandlerInterface::class);
        $eventDispatcher = $this->getMockWithoutConstructor(EventDispatcherInterface::class);

        $authorizationChecker = $this->getMockWithoutConstructor(AuthorizationCheckerInterface::class);
        $authorizationChecker
            ->expects($this->atLeastOnce())
            ->method('isGranted')
            ->willReturnCallback(function ($roles) {
                $this->assertEquals([
                    'ROLE_USER',
                ], $roles);

                return true;
            })
        ;

        $user = $this->getMockWithoutConstructor(UserInterface::class);
        $user
            ->method('getRoles')
            ->willReturn([
                'ROLE_USER',
            ])
        ;

        $token = $this->getMockWithoutConstructor(TokenInterface::class);
        $token
            ->expects($this->once())
            ->method('getUser')
            ->willReturn($user)
        ;
        $tokenStorage = $this->getMockWithoutConstructor(TokenStorageInterface::class);
        $tokenStorage
            ->expects($this->once())
            ->method('getToken')
            ->willReturn($token)
        ;

        $entityLoader = $this->getMockWithoutConstructor(EntityLoaderInterface::class);
        $entityLoader
            ->expects($this->once())
            ->method('load')
            ->with([
                    'deleted' => false,
                ], [
                    'createdAt' => 'desc',
                ],
                10,
                1
            )
            ->willReturn(new ArrayCollection())
        ;

        $actionConfiguration = $this->getMockWithoutConstructor(ActionConfiguration::class);
        $actionConfiguration
            ->method('getParameter')
            ->willReturnMap([
                [
                    'actions', [
                        'list' => [],
                    ],
                ],
                [
                    'criteria', [
                        'deleted',
                    ],
                ],
                [
                    'order', [
                        'createdAt',
                    ],
                ],
                [
                    'max_per_page',
                    10,
                ],
                [
                    'permissions', [
                        'ROLE_USER',
                    ],
                ],
                [
                    'sortable',
                    true,
                ],
            ])
        ;
        $action = $this->getMockWithoutConstructor(ActionInterface::class);
        $action
            ->expects($this->exactly(1))
            ->method('getConfiguration')
            ->willReturn($actionConfiguration)
        ;
        $action
            ->method('isLoadingRequired')
            ->willReturn(true)
        ;
        $request = new Request([
            'deleted' => false,
            'sort' => 'createdAt',
            'order' => 'desc',
        ], [], [
            '_route_params' => [
                '_admin' => 'my_little_pony',
                '_action' => 'list',
            ],
        ]);

        $requestHandler = $this->getMockWithoutConstructor(RequestHandlerInterface::class);
        $requestHandler
            ->expects($this->once())
            ->method('supports')
            ->with($request)
            ->willReturn(true)
        ;

        $view = $this->getMockWithoutConstructor(ViewInterface::class);
        $view
            ->expects($this->exactly(2))
            ->method('getConfiguration')
            ->willReturn($actionConfiguration)
        ;

        $viewFactory = $this->getMockWithoutConstructor(ViewFactory::class);
        $viewFactory
            ->expects($this->once())
            ->method('create')
            ->with('list', 'my_little_pony', $configuration, $actionConfiguration)
            ->willReturn($view)
        ;

        $admin = new Admin(
            'my_little_pony',
            $entityLoader,
            $configuration,
            $messageHandler,
            $eventDispatcher,
            $authorizationChecker,
            $tokenStorage,
            $requestHandler,
            $viewFactory,
            [
                'list' => $action,
            ]
        );
        $admin->handleRequest($request);
    }

    /**
     * An AccessDeniedException should be raised if a non UserInterface is returned by the security token.
     */
    public function testCheckPermissionsNonUser()
    {
        $configuration = $this->getMockWithoutConstructor(AdminConfiguration::class);
        $messageHandler = $this->getMockWithoutConstructor(MessageHandlerInterface::class);
        $eventDispatcher = $this->getMockWithoutConstructor(EventDispatcherInterface::class);
        $authorizationChecker = $this->getMockWithoutConstructor(AuthorizationCheckerInterface::class);

        $token = $this->getMockWithoutConstructor(TokenInterface::class);
        $token
            ->expects($this->once())
            ->method('getUser')
            ->willReturn(false)
        ;
        $tokenStorage = $this->getMockWithoutConstructor(TokenStorageInterface::class);
        $tokenStorage
            ->expects($this->once())
            ->method('getToken')
            ->willReturn($token)
        ;

        $actionConfiguration = $this->getMockWithoutConstructor(ActionConfiguration::class);
        $action = $this->getMockWithoutConstructor(ActionInterface::class);
        $action
            ->expects($this->once())
            ->method('getConfiguration')
            ->willReturn($actionConfiguration)
        ;
        $entityLoader = $this->getMockWithoutConstructor(EntityLoaderInterface::class);

        $request = new Request([], [], [
            '_route_params' => [
                '_admin' => 'my_little_pony',
                '_action' => 'list',
            ],
        ]);
        $requestHandler = $this->getMockWithoutConstructor(RequestHandlerInterface::class);
        $requestHandler
            ->expects($this->once())
            ->method('supports')
            ->with($request)
            ->willReturn(true)
        ;

        $view = $this->getMockWithoutConstructor(ViewInterface::class);
        $viewFactory = $this->getMockWithoutConstructor(ViewFactory::class);
        $viewFactory
            ->expects($this->once())
            ->method('create')
            ->with('list', 'my_little_pony', $configuration, $actionConfiguration)
            ->willReturn($view)
        ;

        $admin = new Admin(
            'my_little_pony',
            $entityLoader,
            $configuration,
            $messageHandler,
            $eventDispatcher,
            $authorizationChecker,
            $tokenStorage,
            $requestHandler,
            $viewFactory,
            [
                'list' => $action,
            ]
        );

        $this->assertExceptionRaised(AccessDeniedException::class, function () use ($request, $admin) {
            $admin->handleRequest($request);
        });
    }

    /**
     * A LogicException should be raised if the current action is not set.
     */
    public function testCheckPermissionsNonInit()
    {
        $configuration = $this->getMockWithoutConstructor(AdminConfiguration::class);
        $messageHandler = $this->getMockWithoutConstructor(MessageHandlerInterface::class);
        $eventDispatcher = $this->getMockWithoutConstructor(EventDispatcherInterface::class);

        $authorizationChecker = $this->getMockWithoutConstructor(AuthorizationCheckerInterface::class);

        $user = $this->getMockWithoutConstructor(UserInterface::class);
        $user
            ->method('getRoles')
            ->willReturn([
                'ROLE_USER',
            ])
        ;

        $token = $this->getMockWithoutConstructor(TokenInterface::class);
        $token
            ->expects($this->once())
            ->method('getUser')
            ->willReturn($user)
        ;
        $tokenStorage = $this->getMockWithoutConstructor(TokenStorageInterface::class);
        $tokenStorage
            ->expects($this->once())
            ->method('getToken')
            ->willReturn($token)
        ;
        $action = $this->getMockWithoutConstructor(ActionInterface::class);
        $entityLoader = $this->getMockWithoutConstructor(EntityLoaderInterface::class);
        $viewFactory = $this->getMockWithoutConstructor(ViewFactory::class);
        $requestHandler = $this->getMockWithoutConstructor(RequestHandlerInterface::class);

        $admin = new Admin(
            'my_little_pony',
            $entityLoader,
            $configuration,
            $messageHandler,
            $eventDispatcher,
            $authorizationChecker,
            $tokenStorage,
            $requestHandler,
            $viewFactory,
            [
                'list' => $action,
            ]
        );

        $this->assertExceptionRaised(LogicException::class, function () use ($admin) {
            $admin->checkPermissions();
        });
    }

    /**
     * A security exception should be raised if the user is not granted.
     */
    public function testCheckPermissionsNonGranted()
    {
        $configuration = $this->getMockWithoutConstructor(AdminConfiguration::class);
        $messageHandler = $this->getMockWithoutConstructor(MessageHandlerInterface::class);
        $eventDispatcher = $this->getMockWithoutConstructor(EventDispatcherInterface::class);

        $authorizationChecker = $this->getMockWithoutConstructor(AuthorizationCheckerInterface::class);
        $authorizationChecker
            ->expects($this->once())
            ->method('isGranted')
            ->willReturn(false)
        ;

        $user = $this->getMockWithoutConstructor(UserInterface::class);
        $user
            ->method('getRoles')
            ->willReturn([
                'ROLE_USER',
            ])
        ;

        $token = $this->getMockWithoutConstructor(TokenInterface::class);
        $token
            ->expects($this->once())
            ->method('getUser')
            ->willReturn($user)
        ;
        $tokenStorage = $this->getMockWithoutConstructor(TokenStorageInterface::class);
        $tokenStorage
            ->expects($this->once())
            ->method('getToken')
            ->willReturn($token)
        ;
        $actionConfiguration = $this->getMockWithoutConstructor(ActionConfiguration::class);
        $action = $this->getMockWithoutConstructor(ActionInterface::class);
        $action
            ->expects($this->once())
            ->method('getConfiguration')
            ->willReturn($actionConfiguration)
        ;

        $entityLoader = $this->getMockWithoutConstructor(EntityLoaderInterface::class);

        $view = $this->getMockWithoutConstructor(ViewInterface::class);

        $viewFactory = $this->getMockWithoutConstructor(ViewFactory::class);
        $viewFactory
            ->expects($this->once())
            ->method('create')
            ->with('list', 'my_little_pony', $configuration, $actionConfiguration)
            ->willReturn($view)
        ;

        $request = new Request([], [], [
            '_route_params' => [
                '_admin' => 'my_little_pony',
                '_action' => 'list',
            ],
        ]);
        $requestHandler = $this->getMockWithoutConstructor(RequestHandlerInterface::class);
        $requestHandler
            ->expects($this->once())
            ->method('supports')
            ->with($request)
            ->willReturn(true)
        ;

        $admin = new Admin(
            'my_little_pony',
            $entityLoader,
            $configuration,
            $messageHandler,
            $eventDispatcher,
            $authorizationChecker,
            $tokenStorage,
            $requestHandler,
            $viewFactory,
            [
                'list' => $action,
            ]
        );

        $this->assertExceptionRaised(AccessDeniedException::class, function () use ($admin, $request) {
            $admin->handleRequest($request);
        });
    }

    /**
     * A security exception should be raised if the user is not granted.
     */
    public function testCheckPermissionsNonGrantedForAction()
    {
        $configuration = $this->getMockWithoutConstructor(AdminConfiguration::class);
        $configuration
            ->method('getParameter')
            ->willReturnMap([
                ['permissions', [
                    'ROLE_USER',
                ]],
            ])
        ;
        $messageHandler = $this->getMockWithoutConstructor(MessageHandlerInterface::class);
        $eventDispatcher = $this->getMockWithoutConstructor(EventDispatcherInterface::class);
        $i = 0;

        $authorizationChecker = $this->getMockWithoutConstructor(AuthorizationCheckerInterface::class);
        $authorizationChecker
            ->expects($this->exactly(2))
            ->method('isGranted')
            ->willReturnCallback(function () use (&$i) {
                ++$i;

                return $i <= 1;
            })
        ;
        $user = $this->getMockWithoutConstructor(UserInterface::class);
        $user
            ->method('getRoles')
            ->willReturn([
                'ROLE_USER',
            ])
        ;
        $token = $this->getMockWithoutConstructor(TokenInterface::class);
        $token
            ->expects($this->once())
            ->method('getUser')
            ->willReturn($user)
        ;
        $tokenStorage = $this->getMockWithoutConstructor(TokenStorageInterface::class);
        $tokenStorage
            ->expects($this->once())
            ->method('getToken')
            ->willReturn($token)
        ;

        $actionConfiguration = $this->getMockWithoutConstructor(ActionConfiguration::class);
        $action = $this->getMockWithoutConstructor(ActionInterface::class);
        $action
            ->expects($this->once())
            ->method('getConfiguration')
            ->willReturn($actionConfiguration)
        ;

        $entityLoader = $this->getMockWithoutConstructor(EntityLoaderInterface::class);

        $view = $this->getMockWithoutConstructor(ViewInterface::class);
        $view
            ->expects($this->once())
            ->method('getConfiguration')
            ->willReturn($actionConfiguration)
        ;

        $viewFactory = $this->getMockWithoutConstructor(ViewFactory::class);
        $viewFactory
            ->expects($this->once())
            ->method('create')
            ->with('list', 'my_little_pony', $configuration, $actionConfiguration)
            ->willReturn($view)
        ;

        $request = new Request([], [], [
            '_route_params' => [
                '_admin' => 'my_little_pony',
                '_action' => 'list',
            ],
        ]);
        $requestHandler = $this->getMockWithoutConstructor(RequestHandlerInterface::class);
        $requestHandler
            ->expects($this->once())
            ->method('supports')
            ->with($request)
            ->willReturn(true)
        ;

        $admin = new Admin(
            'my_little_pony',
            $entityLoader,
            $configuration,
            $messageHandler,
            $eventDispatcher,
            $authorizationChecker,
            $tokenStorage,
            $requestHandler,
            $viewFactory,
            [
                'list' => $action,
            ]
        );

        $this->assertExceptionRaised(AccessDeniedException::class, function () use ($admin, $request) {
            $admin->handleRequest($request);
            $admin->checkPermissions();
        });
    }

    public function testCreate()
    {
        $configuration = $this->getMockWithoutConstructor(AdminConfiguration::class);
        $messageHandler = $this->getMockWithoutConstructor(MessageHandlerInterface::class);
        $eventDispatcher = $this->getMockWithoutConstructor(EventDispatcherInterface::class);
        $authorizationChecker = $this->getMockWithoutConstructor(AuthorizationCheckerInterface::class);
        $tokenStorage = $this->getMockWithoutConstructor(TokenStorageInterface::class);

        $entity = new TestSimpleEntity();

        $dataProvider = $this->getMockWithoutConstructor(DataProviderInterface::class);
        $dataProvider
            ->expects($this->once())
            ->method('create')
            ->willReturn($entity)
        ;

        $entityLoader = $this->getMockWithoutConstructor(EntityLoaderInterface::class);
        $entityLoader
            ->expects($this->atLeastOnce())
            ->method('getDataProvider')
            ->willReturn($dataProvider)
        ;

        $viewFactory = $this->getMockWithoutConstructor(ViewFactory::class);
        $requestHandler = $this->getMockWithoutConstructor(RequestHandlerInterface::class);

        $admin = new Admin(
            'my_little_pony',
            $entityLoader,
            $configuration,
            $messageHandler,
            $eventDispatcher,
            $authorizationChecker,
            $tokenStorage,
            $requestHandler,
            $viewFactory
        );
        $created = $admin->create();

        $this->assertEquals($created, $entity);
        $this->assertEquals($entity, $admin->getEntities()->first());
        $this->assertCount(1, $admin->getEntities());
    }

    public function testSave()
    {
        $configuration = $this->getMockWithoutConstructor(AdminConfiguration::class);
        $eventDispatcher = $this->getMockWithoutConstructor(EventDispatcherInterface::class);
        $authorizationChecker = $this->getMockWithoutConstructor(AuthorizationCheckerInterface::class);
        $tokenStorage = $this->getMockWithoutConstructor(TokenStorageInterface::class);

        $messageHandler = $this->getMockWithoutConstructor(MessageHandlerInterface::class);
        $messageHandler
            ->expects($this->once())
            ->method('handleSuccess')
        ;

        $dataProvider = $this->getMockWithoutConstructor(DataProviderInterface::class);
        $dataProvider
            ->expects($this->exactly(2))
            ->method('save')
        ;

        $entityLoader = $this->getMockWithoutConstructor(EntityLoaderInterface::class);
        $entityLoader
            ->method('getDataProvider')
            ->willReturn($dataProvider)
        ;

        $viewFactory = $this->getMockWithoutConstructor(ViewFactory::class);
        $requestHandler = $this->getMockWithoutConstructor(RequestHandlerInterface::class);

        $admin = new Admin(
            'my_little_pony',
            $entityLoader,
            $configuration,
            $messageHandler,
            $eventDispatcher,
            $authorizationChecker,
            $tokenStorage,
            $requestHandler,
            $viewFactory
        );
        $entities[] = $admin->create();
        $entities[] = $admin->create();
        $admin->save();
    }

    public function testRemove()
    {
        $configuration = $this->getMockWithoutConstructor(AdminConfiguration::class);
        $eventDispatcher = $this->getMockWithoutConstructor(EventDispatcherInterface::class);
        $authorizationChecker = $this->getMockWithoutConstructor(AuthorizationCheckerInterface::class);
        $tokenStorage = $this->getMockWithoutConstructor(TokenStorageInterface::class);

        $messageHandler = $this->getMockWithoutConstructor(MessageHandlerInterface::class);
        $messageHandler
            ->expects($this->once())
            ->method('handleSuccess')
        ;

        $dataProvider = $this->getMockWithoutConstructor(DataProviderInterface::class);
        $dataProvider
            ->expects($this->exactly(2))
            ->method('remove')
        ;

        $entityLoader = $this->getMockWithoutConstructor(EntityLoaderInterface::class);
        $entityLoader
            ->method('getDataProvider')
            ->willReturn($dataProvider)
        ;

        $viewFactory = $this->getMockWithoutConstructor(ViewFactory::class);
        $requestHandler = $this->getMockWithoutConstructor(RequestHandlerInterface::class);

        $admin = new Admin(
            'my_little_pony',
            $entityLoader,
            $configuration,
            $messageHandler,
            $eventDispatcher,
            $authorizationChecker,
            $tokenStorage,
            $requestHandler,
            $viewFactory
        );
        $entities[] = $admin->create();
        $entities[] = $admin->create();
        $admin->remove();
    }

    public function testLoad()
    {
        $configuration = $this->getMockWithoutConstructor(AdminConfiguration::class);
        $messageHandler = $this->getMockWithoutConstructor(MessageHandlerInterface::class);
        $eventDispatcher = $this->getMockWithoutConstructor(EventDispatcherInterface::class);
        $authorizationChecker = $this->getMockWithoutConstructor(AuthorizationCheckerInterface::class);
        $tokenStorage = $this->getMockWithoutConstructor(TokenStorageInterface::class);

        $entityLoader = $this->getMockWithoutConstructor(EntityLoaderInterface::class);
        $entityLoader
            ->method('load')
            ->with([
                'deleted' => false,
            ], [
                'createdAt' => 'desc',
            ], 52, 3)
            ->willReturn([])
        ;

        $view = $this->getMockWithoutConstructor(ViewInterface::class);
        $view
            ->expects($this->once())
            ->method('setEntities')
            ->with(new ArrayCollection())
        ;
        $viewFactory = $this->getMockWithoutConstructor(ViewFactory::class);
        $requestHandler = $this->getMockWithoutConstructor(RequestHandlerInterface::class);
        $action = $this->getMockWithoutConstructor(ActionInterface::class);

        $admin = new Admin(
            'my_little_pony',
            $entityLoader,
            $configuration,
            $messageHandler,
            $eventDispatcher,
            $authorizationChecker,
            $tokenStorage,
            $requestHandler,
            $viewFactory,
            [
                'list' => $action,
            ]
        );

        $reflection = new \ReflectionClass($admin);
        $property = $reflection->getProperty('view');
        $property->setAccessible(true);
        $property->setValue($admin, $view);

        $admin->load([
            'deleted' => false,
        ], [
            'createdAt' => 'desc',
        ], 52, 3);
    }
}
