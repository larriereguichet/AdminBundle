<?php

namespace LAG\AdminBundle\Tests\AdminBundle\Field\Configuration;

use LAG\AdminBundle\Application\Configuration\ApplicationConfiguration;
use LAG\AdminBundle\Field\Configuration\StringFieldConfiguration;
use LAG\AdminBundle\Tests\AdminTestBase;
use Symfony\Component\OptionsResolver\OptionsResolver;

class StringFieldConfigurationTest extends AdminTestBase
{
    public function testConfigureOptions()
    {
        $applicationConfiguration = $this->getMockWithoutConstructor(ApplicationConfiguration::class);
        $applicationConfiguration
            ->expects($this->exactly(2))
            ->method('getParameter')
            ->willReturnMap([
                ['string_length', 20],
                ['string_length_truncate', '...'],
            ])
        ;
    
        $optionsResolver = new OptionsResolver();
        $configuration = new StringFieldConfiguration();
        $configuration->setApplicationConfiguration($applicationConfiguration);
    
        $configuration->configureOptions($optionsResolver);
    
        $options = $optionsResolver->resolve([]);
        
        $this->assertEquals($options['translation'], true);
        $this->assertEquals($options['length'], 20);
        $this->assertEquals($options['replace'], '...');
    }
}
