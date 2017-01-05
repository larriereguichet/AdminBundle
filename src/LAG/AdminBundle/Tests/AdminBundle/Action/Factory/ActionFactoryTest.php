<?php

namespace LAG\AdminBundle\Tests\AdminBundle\Action\Factory;

use LAG\AdminBundle\Action\Configuration\ActionConfiguration;
use LAG\AdminBundle\Action\Factory\ActionFactory;
use LAG\AdminBundle\Action\ActionInterface;
use LAG\AdminBundle\Admin\AdminInterface;
use LAG\AdminBundle\Admin\Configuration\AdminConfiguration;
use LAG\AdminBundle\Configuration\Factory\ConfigurationFactory;
use LAG\AdminBundle\Tests\AdminTestBase;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\EventDispatcher\EventDispatcher;
use Symfony\Component\HttpFoundation\Request;

class ActionFactoryTest extends AdminTestBase
{
    public function testInjectConfiguration()
    {
        $actionConfiguration = $this->getMockWithoutConstructor(ActionConfiguration::class);
        $adminConfiguration = $this->getMockWithoutConstructor(AdminConfiguration::class);
        $adminConfiguration
            ->expects($this->once())
            ->method('getParameter')
            ->with('actions')
            ->willReturn([
                'list' => [
                ]
            ])
        ;
        $admin = $this->getMockWithoutConstructor(AdminInterface::class);
        $admin
            ->expects($this->once())
            ->method('getConfiguration')
            ->willReturn($adminConfiguration)
        ;
        
        $configurationFactory = $this->getMockWithoutConstructor(ConfigurationFactory::class);
        $configurationFactory
            ->expects($this->once())
            ->method('createActionConfiguration')
            ->with('list', $admin)
            ->willReturn($actionConfiguration)
        ;
        $eventDispatcher = $this->getMockWithoutConstructor(EventDispatcher::class);
        
        $controller = $this->getMockWithoutConstructor(ActionInterface::class);
        $controller
            ->expects($this->exactly(5))
            ->method('getAdmin')
            ->willReturn($admin)
        ;
        
        $request = new Request([], [], [
            '_route_params' => [
                '_action' => 'list',
            ]
        ]);
    
        $actionFactory = new ActionFactory(
            $configurationFactory,
            $eventDispatcher
        );
    
        $actionFactory->injectConfiguration($controller, $request);
    }
    
    public function testInjectConfigurationWithoutActions()
    {
        $configurationFactory = $this->getMockWithoutConstructor(ConfigurationFactory::class);
        $eventDispatcher = $this->getMockWithoutConstructor(EventDispatcher::class);
    
        $actionFactory = new ActionFactory(
            $configurationFactory,
            $eventDispatcher
        );
    
        $adminConfiguration = $this->getMockWithoutConstructor(AdminConfiguration::class);
        $adminConfiguration
            ->expects($this->once())
            ->method('getParameter')
            ->with('actions')
            ->willReturn([
                'list' => [
            
                ]
            ])
        ;
    
        $admin = $this->getMockWithoutConstructor(AdminInterface::class);
        $admin
            ->expects($this->once())
            ->method('getConfiguration')
            ->willReturn($adminConfiguration)
        ;
    
        $controller = $this->getMockWithoutConstructor(ActionInterface::class);
        $controller
            ->expects($this->exactly(2))
            ->method('getAdmin')
            ->willReturn($admin)
        ;
        $request = new Request();
    
        $actionFactory->injectConfiguration($controller, $request);
    }
    
    /**
     * If the Controller is not an ActionInterface, nothing should be done.
     */
    public function testInjectConfigurationWithoutActionInterface()
    {
        $configurationFactory = $this->getMockWithoutConstructor(ConfigurationFactory::class);
    
        // no event should be dispatched
        $eventDispatcher = $this->getMockWithoutConstructor(EventDispatcher::class);
        $eventDispatcher
            ->expects($this->never())
            ->method('dispatch')
        ;
        
        $actionFactory = new ActionFactory(
            $configurationFactory,
            $eventDispatcher
        );
        $controller = $this->getMockWithoutConstructor(Controller::class);
        $request = new Request();
        
        $actionFactory->injectConfiguration($controller, $request);
    }
    
    /**
     * If the Controller has no Admin, nothing should be done.
     */
    public function testInjectConfigurationWithoutAdmin()
    {
        $configurationFactory = $this->getMockWithoutConstructor(ConfigurationFactory::class);
    
        // no event should be dispatched
        $eventDispatcher = $this->getMockWithoutConstructor(EventDispatcher::class);
        $eventDispatcher
            ->expects($this->never())
            ->method('dispatch')
        ;
    
        $actionFactory = new ActionFactory(
            $configurationFactory,
            $eventDispatcher
        );
        $controller = $this->getMockWithoutConstructor(ActionInterface::class);
        $request = new Request();
    
        $actionFactory->injectConfiguration($controller, $request);
    }
}
