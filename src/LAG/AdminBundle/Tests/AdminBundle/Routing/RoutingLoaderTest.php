<?php

namespace LAG\AdminBundle\Tests\AdminBundle\Routing;

use LAG\AdminBundle\Action\Configuration\ActionConfiguration;
use LAG\AdminBundle\Action\Factory\ConfigurationFactory as ActionConfigurationFactory;
use LAG\AdminBundle\Admin\Configuration\AdminConfiguration;
use LAG\AdminBundle\Admin\Factory\ConfigurationFactory;
use LAG\AdminBundle\Routing\RoutingLoader;
use LAG\AdminBundle\Tests\AdminTestBase;

class RoutingLoaderTest extends AdminTestBase
{
    public function testLoad()
    {
        $adminConfiguration = $this->getMockWithoutConstructor(AdminConfiguration::class);
        $adminConfiguration
            ->expects($this->exactly(3))
            ->method('getParameter')
            ->willReturnMap([
                ['actions', ['list' => []]],
                ['routing_name_pattern', '{admin'],
            ])
        ;
        $adminConfiguration
            ->expects($this->once())
            ->method('isResolved')
            ->willReturn(true)
        ;

        $adminConfigurationFactory = $this->getMockWithoutConstructor(ConfigurationFactory::class);
        $adminConfigurationFactory
            ->expects($this->exactly(1))
            ->method('create')
            ->willReturnMap([
                [[], $adminConfiguration],
            ])
        ;

        $actionConfiguration = $this->getMockWithoutConstructor(ActionConfiguration::class);
        $actionConfiguration
            ->expects($this->exactly(3))
            ->method('getParameter')
            ->willReturnMap([
                ['route_path', '/test'],
                ['route_defaults', []],
                ['route_requirements', []],
            ])
        ;

        $actionConfigurationFactory = $this->getMockWithoutConstructor(ActionConfigurationFactory::class);
        $actionConfigurationFactory
            ->expects($this->once())
            ->method('create')
            ->willReturn($actionConfiguration)
        ;

        $loader = new RoutingLoader(
            [
                'panda' => [],
            ],
            $adminConfigurationFactory,
            $actionConfigurationFactory
        );
        $loader->load(null);
    }
}
