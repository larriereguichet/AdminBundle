<?php

namespace LAG\AdminBundle\Tests\AdminBundle\Action\Factory;

use LAG\AdminBundle\Action\Configuration\ActionConfiguration;
use LAG\AdminBundle\Action\Factory\ConfigurationFactory;
use LAG\AdminBundle\Admin\Configuration\AdminConfiguration;
use LAG\AdminBundle\Tests\AdminTestBase;

class ConfigurationFactoryTest extends AdminTestBase
{
    public function testCreate()
    {
        $configuration = $this->getMockWithoutConstructor(AdminConfiguration::class);
        $configuration
            ->expects($this->once())
            ->method('isResolved')
            ->willReturn(true)
        ;
        $configuration
            ->expects($this->exactly(6))
            ->method('getParameter')
            ->willReturnMap([
                ['actions', [
                    'my-action' => [],
                ]],
                ['translation_pattern', 'test'],
                ['routing_url_pattern', 'test'],
                ['max_per_page', 10],
                ['form', 'FormType'],
            ])
        ;
        
        $factory = new ConfigurationFactory();
        $actionConfiguration = $factory->create('my-action', 'my-admin', $configuration, []);
    
        $this->assertInstanceOf(ActionConfiguration::class, $actionConfiguration);
    }
}
