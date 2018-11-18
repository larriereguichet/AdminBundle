<?php

namespace LAG\AdminBundle\Tests\Factory;

use LAG\AdminBundle\Admin\Admin;
use LAG\AdminBundle\Configuration\AdminConfiguration;
use LAG\AdminBundle\Configuration\ApplicationConfiguration;
use LAG\AdminBundle\Configuration\ApplicationConfigurationStorage;
use LAG\AdminBundle\Exception\Exception;
use LAG\AdminBundle\Factory\AdminFactory;
use LAG\AdminBundle\Factory\ConfigurationFactory;
use LAG\AdminBundle\Resource\AdminResource;
use LAG\AdminBundle\Resource\ResourceCollection;
use LAG\AdminBundle\Tests\AdminTestBase;
use LAG\AdminBundle\Tests\Fixtures\FakeAdmin;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\HttpFoundation\Request;

class AdminFactoryTest extends AdminTestBase
{
    public function testCreateFromRequest()
    {
        $resource = $this->getMockWithoutConstructor(AdminResource::class);
        $resource
            ->expects($this->atLeastOnce())
            ->method('getName')
            ->willReturn('MyLittleTaunTaun')
        ;

        $resourceCollection = $this->getMockWithoutConstructor(ResourceCollection::class);
        $resourceCollection
            ->expects($this->once())
            ->method('has')
            ->with('MyLittleTaunTaun')
            ->willReturn(true)
        ;
        $resourceCollection
            ->expects($this->once())
            ->method('get')
            ->with('MyLittleTaunTaun')
            ->willReturn($resource)
        ;

        $adminConfiguration = $this->getMockWithoutConstructor(AdminConfiguration::class);
        $adminConfiguration
            ->expects($this->once())
            ->method('getParameter')
            ->with('class')
            ->willReturn(Admin::class)
        ;

        $configurationFactory = $this->getMockWithoutConstructor(ConfigurationFactory::class);
        $configurationFactory
            ->expects($this->once())
            ->method('createAdminConfiguration')
            ->with('MyLittleTaunTaun')
            ->willReturn($adminConfiguration)
        ;

        $eventDispatcher = $this->getMockWithoutConstructor(EventDispatcherInterface::class);

        $applicationConfiguration = new ApplicationConfiguration();
        $applicationConfigurationStorage = $this->getMockWithoutConstructor(ApplicationConfigurationStorage::class);
        $applicationConfigurationStorage
            ->expects($this->once())
            ->method('getConfiguration')
            ->willReturn($applicationConfiguration)
        ;

        $request = new Request([
            '_admin' => 'MyLittleTaunTaun',
            '_route_params' => [
                '_admin' => 'MyLittleTaunTaun',
                '_action' => 'jump',
            ],
        ]);

        $adminFactory = new AdminFactory(
            $resourceCollection,
            $eventDispatcher,
            $configurationFactory,
            $applicationConfigurationStorage
        );

        $adminFactory->createFromRequest($request);
    }

    public function testCreateFromRequestWithoutRouteParams()
    {
        $resourceCollection = $this->getMockWithoutConstructor(ResourceCollection::class);
        $eventDispatcher = $this->getMockWithoutConstructor(EventDispatcherInterface::class);
        $configurationFactory = $this->getMockWithoutConstructor(ConfigurationFactory::class);
        $applicationConfigurationStorage = $this->getMockWithoutConstructor(ApplicationConfigurationStorage::class);

        $request = new Request();

        $adminFactory = new AdminFactory(
            $resourceCollection,
            $eventDispatcher,
            $configurationFactory,
            $applicationConfigurationStorage
        );

        $this->expectException(Exception::class);
        $adminFactory->createFromRequest($request);
    }

    public function testCreateFromRequestWithoutAdminParams()
    {
        $resourceCollection = $this->getMockWithoutConstructor(ResourceCollection::class);
        $eventDispatcher = $this->getMockWithoutConstructor(EventDispatcherInterface::class);
        $configurationFactory = $this->getMockWithoutConstructor(ConfigurationFactory::class);
        $applicationConfigurationStorage = $this->getMockWithoutConstructor(ApplicationConfigurationStorage::class);

        $request = new Request([
            '_route_params' => [],
        ]);

        $adminFactory = new AdminFactory(
            $resourceCollection,
            $eventDispatcher,
            $configurationFactory,
            $applicationConfigurationStorage
        );

        $this->expectException(Exception::class);
        $adminFactory->createFromRequest($request);
    }

    public function testCreateFromRequestWithoutExistingAdmin()
    {
        $resourceCollection = $this->getMockWithoutConstructor(ResourceCollection::class);
        $eventDispatcher = $this->getMockWithoutConstructor(EventDispatcherInterface::class);
        $configurationFactory = $this->getMockWithoutConstructor(ConfigurationFactory::class);
        $applicationConfigurationStorage = $this->getMockWithoutConstructor(ApplicationConfigurationStorage::class);

        $request = new Request([
            '_route_params' => [
                '_admin' => 'MyLittleTaunTaun',
                '_action' => 'jump',
            ],
        ]);

        $adminFactory = new AdminFactory(
            $resourceCollection,
            $eventDispatcher,
            $configurationFactory,
            $applicationConfigurationStorage
        );

        $this->expectException(Exception::class);
        $adminFactory->createFromRequest($request);
    }

    public function testCreateFromRequestWithInvalidAdminClass()
    {
        $resource = $this->getMockWithoutConstructor(AdminResource::class);
        $resource
            ->expects($this->atLeastOnce())
            ->method('getName')
            ->willReturn('MyLittleTaunTaun')
        ;

        $resourceCollection = $this->getMockWithoutConstructor(ResourceCollection::class);
        $resourceCollection
            ->expects($this->once())
            ->method('has')
            ->with('MyLittleTaunTaun')
            ->willReturn(true)
        ;
        $resourceCollection
            ->expects($this->once())
            ->method('get')
            ->with('MyLittleTaunTaun')
            ->willReturn($resource)
        ;

        $adminConfiguration = $this->getMockWithoutConstructor(AdminConfiguration::class);
        $adminConfiguration
            ->expects($this->once())
            ->method('getParameter')
            ->with('class')
            ->willReturn(FakeAdmin::class)
        ;

        $configurationFactory = $this->getMockWithoutConstructor(ConfigurationFactory::class);
        $configurationFactory
            ->expects($this->once())
            ->method('createAdminConfiguration')
            ->with('MyLittleTaunTaun')
            ->willReturn($adminConfiguration)
        ;

        $eventDispatcher = $this->getMockWithoutConstructor(EventDispatcherInterface::class);

        $applicationConfiguration = new ApplicationConfiguration();
        $applicationConfigurationStorage = $this->getMockWithoutConstructor(ApplicationConfigurationStorage::class);
        $applicationConfigurationStorage
            ->expects($this->once())
            ->method('getConfiguration')
            ->willReturn($applicationConfiguration)
        ;

        $request = new Request([
            '_admin' => 'MyLittleTaunTaun',
            '_route_params' => [
                '_admin' => 'MyLittleTaunTaun',
                '_action' => 'jump',
            ],
        ]);

        $adminFactory = new AdminFactory(
            $resourceCollection,
            $eventDispatcher,
            $configurationFactory,
            $applicationConfigurationStorage
        );

        $this->expectException(Exception::class);
        $adminFactory->createFromRequest($request);
    }
}
