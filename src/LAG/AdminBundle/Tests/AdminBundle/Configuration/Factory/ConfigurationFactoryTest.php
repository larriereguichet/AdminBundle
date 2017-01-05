<?php

namespace LAG\AdminBundle\Tests\AdminBundle\Configuration\Factory;

use LAG\AdminBundle\Action\Configuration\ActionConfiguration;
use LAG\AdminBundle\Admin\AdminInterface;
use LAG\AdminBundle\Admin\Configuration\AdminConfiguration;
use LAG\AdminBundle\Application\Configuration\ApplicationConfiguration;
use LAG\AdminBundle\Configuration\Factory\ConfigurationFactory;
use LAG\AdminBundle\Tests\AdminTestBase;
use LAG\AdminBundle\Tests\Entity\TestEntity;
use Twig_Environment;

class ConfigurationFactoryTest extends AdminTestBase
{
    /**
     * On the factory constructor, the array configuration should be resolved and set into the factory.
     */
    public function testConstruct()
    {
        $twig = $this->getMockWithoutConstructor(Twig_Environment::class);
        
        $factory = new ConfigurationFactory([
            'title' => 'My Little Tauntaun',
        ], $twig);
    
        // the configuration should be resolved and set
        $this->assertInstanceOf(ApplicationConfiguration::class, $factory->getApplicationConfiguration());
        $this->assertEquals('My Little Tauntaun', $factory->getApplicationConfiguration()->getParameter('title'));
    }
    
    /**
     * The createActionConfiguration should return an ActionConfiguration object with resolved configuration.
     */
    public function testCreateActionConfiguration()
    {
        $twig = $this->getMockWithoutConstructor(Twig_Environment::class);
    
        $adminConfiguration = $this->getMockWithoutConstructor(AdminConfiguration::class);
        $adminConfiguration
            ->expects($this->atLeastOnce())
            ->method('getParameter')
            ->willReturnMap([
                [],
            ])
        ;
        
        $admin = $this->getMockWithoutConstructor(AdminInterface::class);
        $admin
            ->expects($this->atLeastOnce())
            ->method('getConfiguration')
            ->willReturn($adminConfiguration)
        ;
        $admin
            ->expects($this->once())
            ->method('generateRouteName')
            ->with('list')
            ->willReturn('lag.admin.list')
        ;
    
        $factory = new ConfigurationFactory([
            'title' => 'My Little Tauntaun',
        ], $twig);
    
        $configuration = $factory->createActionConfiguration('list', $admin, []);
    
        $this->assertInstanceOf(ActionConfiguration::class, $configuration);
        $this->assertEquals('List', $configuration->getParameter('title'));
    }
    
    /**
     * The createAdminConfiguration should return an AdminConfiguration object with resolved configuration.
     */
    public function testCreateAdminConfiguration()
    {
        $twig = $this->getMockWithoutConstructor(Twig_Environment::class);
    
        $factory = new ConfigurationFactory([
            'title' => 'My Little Tauntaun',
        ], $twig);
        
        $configuration = $factory->createAdminConfiguration([
            'entity' => TestEntity::class,
        ]);
    
        $this->assertInstanceOf(AdminConfiguration::class, $configuration);
        $this->assertEquals(TestEntity::class, $configuration->getParameter('entity'));
    }
}
