<?php

namespace LAG\AdminBundle\Tests\AdminBundle\Configuration;

use LAG\AdminBundle\Application\Configuration\ApplicationConfiguration;
use LAG\AdminBundle\Tests\AdminTestBase;
use Symfony\Component\DependencyInjection\Exception\LogicException;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ConfigurationTest extends AdminTestBase
{
    public function testHasParameter()
    {
        $configuration = new ApplicationConfiguration();
        
        // with no resolved configuration, the method should return false
        $this->assertFalse($configuration->hasParameter('a_parameter'));
    
        // with a resolved configuration should return true if a right parameter is provided
        $resolver = new OptionsResolver();
        $configuration->configureOptions($resolver);
        $resolved = $resolver->resolve([
            'title' => 'My Little Tauntaun',
        ]);
        $configuration->setParameters($resolved);
        
        $this->assertFalse($configuration->hasParameter('a_parameter'));
        $this->assertTrue($configuration->hasParameter('title'));
        $this->assertTrue($configuration->isResolved());
    }
    
    public function testSetParameters()
    {
        $configuration = new ApplicationConfiguration();
        $configuration->setParameters([
            'title' => 'Planet Application',
        ]);
    
        $this->assertExceptionRaised(LogicException::class, function () use ($configuration) {
            $configuration->setParameters([
                'an_other_configuration' => 'value',
            ]);
        });
    }
    
    public function testGetParameters()
    {
        $configuration = new ApplicationConfiguration();
        $configuration->setParameters([
            'title' => 'Planet Application',
        ]);
    
        $this->assertInternalType('array', $configuration->getParameters());
    }
}
