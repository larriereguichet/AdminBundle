<?php

namespace LAG\AdminBundle\Tests\AdminBundle\Field\Configuration;

use LAG\AdminBundle\Field\Configuration\ActionConfiguration;
use LAG\AdminBundle\Tests\AdminTestBase;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ActionConfigurationTest extends AdminTestBase
{
    public function testConfigureOptions()
    {
        $optionsResolver = new OptionsResolver();
        $configuration = new ActionConfiguration();
        $configuration->configureOptions($optionsResolver);
    
        $options = $optionsResolver->resolve([]);
        
        $this->assertEquals($options['text'], '');
        $this->assertEquals($options['class'], 'btn btn-danger btn-sm');
    }
}
