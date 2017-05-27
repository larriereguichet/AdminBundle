<?php

namespace LAG\AdminBundle\Tests\AdminBundle\Admin;

use Exception;
use LAG\AdminBundle\Action\ActionInterface;
use LAG\AdminBundle\Action\Configuration\ActionConfiguration;
use LAG\AdminBundle\Admin\Admin;
use LAG\AdminBundle\Admin\Configuration\AdminConfiguration;
use LAG\AdminBundle\Message\MessageHandlerInterface;
use LAG\AdminBundle\Repository\RepositoryInterface;
use LAG\AdminBundle\Tests\AdminTestBase;
use LAG\AdminBundle\Tests\Entity\TestSimpleEntity;
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
    
        $repository = $this->getMockWithoutConstructor(RepositoryInterface::class);
        $repository
            ->expects($this->once())
            ->method('findBy')
            ->with([], [])
        ;
        
        $authorizationChecker = $this->getMockWithoutConstructor(AuthorizationCheckerInterface::class);
        $authorizationChecker
            ->expects($this->atLeastOnce())
            ->method('isGranted')
            ->willReturnCallback(function($roles) {
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
    
        $actionConfiguration = $this->getMockWithoutConstructor(ActionConfiguration::class);
        $actionConfiguration
            ->method('getParameter')
            ->willReturnMap([
                ['criteria', []],
                ['order', []],
                ['max_per_page', []],
                ['permissions', ['ROLE_USER']],
            ])
        ;
        $action = $this->getMockWithoutConstructor(ActionInterface::class);
        $action
            ->expects($this->once())
            ->method('getName')
            ->willReturn('list')
        ;
        $action
            ->expects($this->exactly(2))
            ->method('getConfiguration')
            ->willReturn($actionConfiguration)
        ;
        $action
            ->method('isLoadingRequired')
            ->willReturn(true)
        ;
    
        $admin = new Admin(
            'my_little_pony',
            $repository,
            $configuration,
            $messageHandler,
            $eventDispatcher,
            $authorizationChecker,
            $tokenStorage
        );
        $admin->addAction($action);
        
        $request = new Request([], [], [
            '_route_params' => [
                '_admin' => 'my_little_pony',
                '_action' => 'list',
            ]
        ]);
    
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
    
        $repository = $this->getMockWithoutConstructor(RepositoryInterface::class);
    
        $admin = new Admin(
            'my_little_pony',
            $repository,
            $configuration,
            $messageHandler,
            $eventDispatcher,
            $authorizationChecker,
            $tokenStorage
        );
        $action = $this->getMockWithoutConstructor(ActionInterface::class);
        $action
            ->expects($this->once())
            ->method('getName')
            ->willReturn('list')
        ;
        $admin->addAction($action);
        $request = new Request([], [], [
            '_route_params' => [
                '_admin' => 'my_little_pony',
                '_action' => 'list',
            ]
        ]);
    
        $this->assertExceptionRaised(AccessDeniedException::class, function() use ($request, $admin) {
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
    
        $repository = $this->getMockWithoutConstructor(RepositoryInterface::class);
        
        $admin = new Admin(
            'my_little_pony',
            $repository,
            $configuration,
            $messageHandler,
            $eventDispatcher,
            $authorizationChecker,
            $tokenStorage
        );
        $action = $this->getMockWithoutConstructor(ActionInterface::class);
        $action
            ->expects($this->once())
            ->method('getName')
            ->willReturn('list')
        ;
        $admin->addAction($action);
        
        $this->assertExceptionRaised(LogicException::class, function() use ($admin) {
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
    
        $repository = $this->getMockWithoutConstructor(RepositoryInterface::class);
        
        $admin = new Admin(
            'my_little_pony',
            $repository,
            $configuration,
            $messageHandler,
            $eventDispatcher,
            $authorizationChecker,
            $tokenStorage
        );
        $action = $this->getMockWithoutConstructor(ActionInterface::class);
        $action
            ->expects($this->once())
            ->method('getName')
            ->willReturn('list')
        ;
        $admin->addAction($action);
        $request = new Request([], [], [
            '_route_params' => [
                '_admin' => 'my_little_pony',
                '_action' => 'list',
            ]
        ]);
        
        $this->assertExceptionRaised(AccessDeniedException::class, function() use ($admin, $request) {
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
                $i++;
    
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
    
        $repository = $this->getMockWithoutConstructor(RepositoryInterface::class);
        
        $admin = new Admin(
            'my_little_pony',
            $repository,
            $configuration,
            $messageHandler,
            $eventDispatcher,
            $authorizationChecker,
            $tokenStorage
        );
    
        $actionConfiguration = $this->getMockWithoutConstructor(ActionConfiguration::class);
        $actionConfiguration
            ->method('getParameter')
            ->willReturnMap([
                ['permissions', []],
            ])
        ;

        $action = $this->getMockWithoutConstructor(ActionInterface::class);
        $action
            ->expects($this->once())
            ->method('getName')
            ->willReturn('list')
        ;
        $action
            ->method('getConfiguration')
            ->willReturn($actionConfiguration)
        ;
        
        $admin->addAction($action);
        $request = new Request([], [], [
            '_route_params' => [
                '_admin' => 'my_little_pony',
                '_action' => 'list',
            ]
        ]);
    
        $this->assertExceptionRaised(AccessDeniedException::class, function() use ($admin, $request) {
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
    
        $repository = $this->getMockWithoutConstructor(RepositoryInterface::class);
        $repository
            ->expects($this->once())
            ->method('create')
            ->willReturn($entity)
        ;
    
        $admin = new Admin(
            'my_little_pony',
            $repository,
            $configuration,
            $messageHandler,
            $eventDispatcher,
            $authorizationChecker,
            $tokenStorage
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
    
        $repository = $this->getMockWithoutConstructor(RepositoryInterface::class);
        $repository
            ->expects($this->exactly(2))
            ->method('save')
        ;
        
        $admin = new Admin(
            'my_little_pony',
            $repository,
            $configuration,
            $messageHandler,
            $eventDispatcher,
            $authorizationChecker,
            $tokenStorage
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
    
        $repository = $this->getMockWithoutConstructor(RepositoryInterface::class);
        $repository
            ->expects($this->exactly(2))
            ->method('delete')
        ;
    
        $admin = new Admin(
            'my_little_pony',
            $repository,
            $configuration,
            $messageHandler,
            $eventDispatcher,
            $authorizationChecker,
            $tokenStorage
        );
        $entities[] = $admin->create();
        $entities[] = $admin->create();
        $admin->remove();
    }
    
    public function testGenerateRouteName()
    {
        $eventDispatcher = $this->getMockWithoutConstructor(EventDispatcherInterface::class);
        $authorizationChecker = $this->getMockWithoutConstructor(AuthorizationCheckerInterface::class);
        $tokenStorage = $this->getMockWithoutConstructor(TokenStorageInterface::class);
    
        $configuration = $this->getMockWithoutConstructor(AdminConfiguration::class);
        $configuration
            ->expects($this->atLeastOnce())
            ->method('getParameter')
            ->willReturnMap([
                ['actions', [
                    'list' => [],
                ]],
                ['routing_name_pattern', 'test.{admin}.{action}'],
            ])
        ;
        
        $messageHandler = $this->getMockWithoutConstructor(MessageHandlerInterface::class);
    
        $repository = $this->getMockWithoutConstructor(RepositoryInterface::class);
        
        $admin = new Admin(
            'my_little_pony',
            $repository,
            $configuration,
            $messageHandler,
            $eventDispatcher,
            $authorizationChecker,
            $tokenStorage
        );
    
        $routeName = $admin->generateRouteName('list');
        $this->assertEquals('test.my_little_pony.list', $routeName);
    
        $this->assertExceptionRaised(Exception::class, function() use ($admin) {
            $admin->generateRouteName('wrong');
        });
    }
    
    public function testLoad()
    {
        $configuration = $this->getMockWithoutConstructor(AdminConfiguration::class);
        $messageHandler = $this->getMockWithoutConstructor(MessageHandlerInterface::class);
        $eventDispatcher = $this->getMockWithoutConstructor(EventDispatcherInterface::class);
        
        $authorizationChecker = $this->getMockWithoutConstructor(AuthorizationCheckerInterface::class);
        $authorizationChecker
            ->expects($this->atLeastOnce())
            ->method('isGranted')
            ->willReturnCallback(function($roles) {
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
    
        $repository = $this->getMockWithoutConstructor(RepositoryInterface::class);
        $repository
            ->expects($this->once())
            ->method('findBy')
            ->with([], [])
            ->willReturn([])
        ;
        
        $admin = new Admin(
            'my_little_pony',
            $repository,
            $configuration,
            $messageHandler,
            $eventDispatcher,
            $authorizationChecker,
            $tokenStorage
        );
        
        $actionConfiguration = $this->getMockWithoutConstructor(ActionConfiguration::class);
        $actionConfiguration
            ->expects($this->atLeastOnce())
            ->method('getParameter')
            ->willReturnMap([
                ['criteria', []],
                ['order', []],
                ['max_per_page', []],
                ['permissions', [
                    'ROLE_USER',
                ]],
            ])
        ;
        $action = $this->getMockWithoutConstructor(ActionInterface::class);
        $action
            ->expects($this->once())
            ->method('getName')
            ->willReturn('list')
        ;
        $action
            ->expects($this->atLeastOnce())
            ->method('getConfiguration')
            ->willReturn($actionConfiguration)
        ;
        $admin->addAction($action);
        $request = new Request([], [], [
            '_route_params' => [
                '_admin' => 'my_little_pony',
                '_action' => 'list',
            ]
        ]);
        
        $admin->handleRequest($request);
    
        $admin->load([
            
        ]);
    }
}
