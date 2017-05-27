<?php

namespace LAG\AdminBundle\Tests\AdminBundle\Admin\Factory;

use LAG\AdminBundle\Action\Factory\ActionFactory;
use LAG\AdminBundle\Action\ListAction;
use LAG\AdminBundle\Admin\Admin;
use LAG\AdminBundle\Admin\Configuration\AdminConfiguration;
use LAG\AdminBundle\Admin\AdminInterface;
use LAG\AdminBundle\Admin\Event\AdminEvents;
use LAG\AdminBundle\Admin\Event\AdminInjectedEvent;
use LAG\AdminBundle\Admin\Factory\AdminFactory;
use LAG\AdminBundle\Admin\Registry\Registry;
use LAG\AdminBundle\Admin\Request\RequestHandler;
use LAG\AdminBundle\Application\Configuration\ApplicationConfiguration;
use LAG\AdminBundle\Configuration\Factory\ConfigurationFactory;
use LAG\AdminBundle\Doctrine\Repository\DoctrineRepositoryFactory;
use LAG\AdminBundle\Message\MessageHandlerInterface;
use LAG\AdminBundle\Repository\RepositoryInterface;
use LAG\AdminBundle\Tests\AdminTestBase;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\EventDispatcher\EventDispatcher;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;

class AdminFactoryTest extends AdminTestBase
{
    /**
     * Init method should create Admin object according to given configuration.
     */
    public function testInit()
    {
        $adminConfiguration = [
            'my_admin' => [
                'entity' => 'TestClass',
                'actions' => [
                    'test' => [
                        'service' => 'test',
                    ],
                ]
            ],
        ];
        $entity = 'TestClass';
        $dataProviderString = 'data_provider';
        $eventDispatcher = $this->getMockWithoutConstructor(EventDispatcher::class);
        $eventDispatcher
            ->expects($this->exactly(3))
            ->method('dispatch')
        ;
    
        $adminConfigurationObject = $this->getMockWithoutConstructor(AdminConfiguration::class);
        $adminConfigurationObject
            ->expects($this->atLeastOnce())
            ->method('getParameter')
            ->willReturnMap([
                ['entity', $entity],
                ['repository', $dataProviderString],
            ])
        ;
    
        $applicationConfigurationObject = $this->getMockWithoutConstructor(ApplicationConfiguration::class);
        $applicationConfigurationObject
            ->expects($this->atLeastOnce())
            ->method('getParameter')
            ->willReturnMap([
                ['admin_class', Admin::class],
            ])
        ;
        
        $configurationFactory = $this->getMockWithoutConstructor(ConfigurationFactory::class);
        $configurationFactory
            ->expects($this->once())
            ->method('createAdminConfiguration')
            ->with($adminConfiguration['my_admin'])
            ->willReturn($adminConfigurationObject)
        ;
        $configurationFactory
            ->expects($this->atLeastOnce())
            ->method('getApplicationConfiguration')
            ->willReturn($applicationConfigurationObject)
        ;
        
        $actionFactory = $this->getMockWithoutConstructor(ActionFactory::class);
        $messageHandler = $this->getMockWithoutConstructor(MessageHandlerInterface::class);
        $registry = $this->getMockWithoutConstructor(Registry::class);
        
    
        $repository = $this->getMockWithoutConstructor(RepositoryInterface::class);
        
        $repositoryFactory = $this->getMockWithoutConstructor(DoctrineRepositoryFactory::class);
        $repositoryFactory
            ->expects($this->once())
            ->method('get')
            ->with('data_provider')
            ->willReturn($repository)
        ;
    
        $requestHandler = $this->getMockWithoutConstructor(RequestHandler::class);
        $authorizationChecker = $this->getMockWithoutConstructor(AuthorizationCheckerInterface::class);
        $tokenStorage = $this->getMockWithoutConstructor(TokenStorageInterface::class);
    
        $actionRegistry = $this->getMockWithoutConstructor(\LAG\AdminBundle\Action\Registry\Registry::class);
        
        $factory = new AdminFactory(
            $adminConfiguration,
            $eventDispatcher,
            $messageHandler,
            $registry,
            $actionRegistry,
            $actionFactory,
            $configurationFactory,
            $repositoryFactory,
            $requestHandler,
            $authorizationChecker,
            $tokenStorage
        );
    
        $factory->init();
        $this->assertTrue($factory->isInit());
        
        // second init should do nothing
        $factory->init();
        $this->assertTrue($factory->isInit());
    }
    
    public function testInjectAdmin()
    {
        $request = new Request();
        $adminConfiguration = [
            'my_admin' => [
                'entity' => 'TestClass'
            ],
        ];
        $eventDispatcher = $this->getMockWithoutConstructor(EventDispatcher::class);
        $eventDispatcher
            ->expects($this->once())
            ->method('dispatch')
            ->willReturnCallback(function($name, $event) {
                $this->assertEquals(AdminEvents::ADMIN_INJECTED, $name);
                $this->assertInstanceOf(AdminInjectedEvent::class, $event);
            })
        ;
        $configurationFactory = $this->getMockWithoutConstructor(ConfigurationFactory::class);
    
        $actionFactory = $this->getMockWithoutConstructor(ActionFactory::class);
    
        $messageHandler = $this->getMockWithoutConstructor(MessageHandlerInterface::class);
    
        $registry = $this->getMockWithoutConstructor(Registry::class);
    
        $repositoryFactory = $this->getMockWithoutConstructor(DoctrineRepositoryFactory::class);
    
        $admin = $this->getMockWithoutConstructor(AdminInterface::class);
        
        $requestHandler = $this->getMockWithoutConstructor(RequestHandler::class);
        $requestHandler
            ->expects($this->once())
            ->method('handle')
            ->with($request)
            ->willReturn($admin)
        ;
   
        $authorizationChecker = $this->getMockWithoutConstructor(AuthorizationCheckerInterface::class);
        $tokenStorage = $this->getMockWithoutConstructor(TokenStorageInterface::class);
    
        $actionRegistry = $this->getMockWithoutConstructor(\LAG\AdminBundle\Action\Registry\Registry::class);
    
        $factory = new AdminFactory(
            $adminConfiguration,
            $eventDispatcher,
            $messageHandler,
            $registry,
            $actionRegistry,
            $actionFactory,
            $configurationFactory,
            $repositoryFactory,
            $requestHandler,
            $authorizationChecker,
            $tokenStorage
        );
    
        $controller = $this->getMockWithoutConstructor(ListAction::class);
    
        // injectAdmin should inject an Admin in Twig global parameters and dispatch an event
        $factory->injectAdmin($controller, $request);
    
        // with a non AdminAware controller, it should do nothing
        $controller = $this->getMockWithoutConstructor(Controller::class);
        $factory->injectAdmin($controller, $request);
    }
}
