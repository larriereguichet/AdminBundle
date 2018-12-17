<?php

namespace LAG\AdminBundle\Tests\AdminBundle\Admin\Factory;

use LAG\AdminBundle\Action\Factory\ActionFactory;
use LAG\AdminBundle\Controller\ListAction;
use LAG\AdminBundle\Admin\Admin;
use LAG\AdminBundle\Admin\Configuration\AdminConfiguration;
use LAG\AdminBundle\Admin\AdminInterface;
use LAG\AdminBundle\Admin\Event\AdminEvents;
use LAG\AdminBundle\Admin\Event\AdminInjectedEvent;
use LAG\AdminBundle\Admin\Factory\AdminFactory;
use LAG\AdminBundle\Admin\Factory\ConfigurationFactory;
use LAG\AdminBundle\Admin\Registry\Registry;
use LAG\AdminBundle\Admin\Request\RequestHandler;
use LAG\AdminBundle\Application\Configuration\ApplicationConfiguration;
use LAG\AdminBundle\Application\Configuration\ApplicationConfigurationStorage;
use LAG\AdminBundle\DataProvider\DataProviderInterface;
use LAG\AdminBundle\DataProvider\Factory\DataProviderFactory;
use LAG\AdminBundle\Message\MessageHandlerInterface;
use LAG\AdminBundle\Tests\AdminTestBase;
use LAG\AdminBundle\View\Factory\ViewFactory;
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
                ],
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
                ['data_provider', $dataProviderString],
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
            ->method('create')
            ->with($adminConfiguration['my_admin'])
            ->willReturn($adminConfigurationObject)
        ;

        $actionFactory = $this->getMockWithoutConstructor(ActionFactory::class);
        $messageHandler = $this->getMockWithoutConstructor(MessageHandlerInterface::class);
        $registry = $this->getMockWithoutConstructor(Registry::class);

        $dataProvider = $this->getMockWithoutConstructor(DataProviderInterface::class);

        $dataProviderFactory = $this->getMockWithoutConstructor(DataProviderFactory::class);
        $dataProviderFactory
            ->expects($this->once())
            ->method('get')
            ->with('data_provider')
            ->willReturn($dataProvider)
        ;

        $requestHandler = $this->getMockWithoutConstructor(RequestHandler::class);
        $authorizationChecker = $this->getMockWithoutConstructor(AuthorizationCheckerInterface::class);
        $tokenStorage = $this->getMockWithoutConstructor(TokenStorageInterface::class);

        $actionRegistry = $this->getMockWithoutConstructor(\LAG\AdminBundle\Action\Registry\Registry::class);
        $viewFactory = $this->getMockWithoutConstructor(ViewFactory::class);

        $applicationConfigurationStorage = $this->getMockWithoutConstructor(ApplicationConfigurationStorage::class);
        $applicationConfigurationStorage
            ->expects($this->once())
            ->method('getApplicationConfiguration')
            ->willReturn($applicationConfigurationObject)
        ;

        $factory = new AdminFactory(
            $adminConfiguration,
            $eventDispatcher,
            $messageHandler,
            $registry,
            $actionRegistry,
            $actionFactory,
            $configurationFactory,
            $dataProviderFactory,
            $viewFactory,
            $requestHandler,
            $authorizationChecker,
            $tokenStorage,
            $applicationConfigurationStorage
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
                'entity' => 'TestClass',
            ],
        ];
        $eventDispatcher = $this->getMockWithoutConstructor(EventDispatcher::class);
        $eventDispatcher
            ->expects($this->once())
            ->method('dispatch')
            ->willReturnCallback(function ($name, $event) {
                $this->assertEquals(AdminEvents::ADMIN_INJECTED, $name);
                $this->assertInstanceOf(AdminInjectedEvent::class, $event);
            })
        ;
        $configurationFactory = $this->getMockWithoutConstructor(ConfigurationFactory::class);

        $actionFactory = $this->getMockWithoutConstructor(ActionFactory::class);

        $messageHandler = $this->getMockWithoutConstructor(MessageHandlerInterface::class);

        $registry = $this->getMockWithoutConstructor(Registry::class);

        $dataProviderFactory = $this->getMockWithoutConstructor(DataProviderFactory::class);

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
        $viewFactory = $this->getMockWithoutConstructor(ViewFactory::class);
        $applicationConfigurationStorage = $this->getMockWithoutConstructor(ApplicationConfigurationStorage::class);

        $factory = new AdminFactory(
            $adminConfiguration,
            $eventDispatcher,
            $messageHandler,
            $registry,
            $actionRegistry,
            $actionFactory,
            $configurationFactory,
            $dataProviderFactory,
            $viewFactory,
            $requestHandler,
            $authorizationChecker,
            $tokenStorage,
            $applicationConfigurationStorage
        );

        $controller = $this->getMockWithoutConstructor(ListAction::class);

        // injectAdmin should inject an Admin in Twig global parameters and dispatch an event
        $factory->injectAdmin($controller, $request);

        // with a non AdminAware controller, it should do nothing
        $controller = $this->getMockWithoutConstructor(Controller::class);
        $factory->injectAdmin($controller, $request);
    }
}
