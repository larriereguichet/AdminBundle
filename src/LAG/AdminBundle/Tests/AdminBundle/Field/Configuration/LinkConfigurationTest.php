<?php

namespace LAG\AdminBundle\Tests\AdminBundle\Field\Configuration;

use LAG\AdminBundle\Application\Configuration\ApplicationConfiguration;
use LAG\AdminBundle\Field\AbstractField;
use LAG\AdminBundle\Field\Configuration\LinkConfiguration;
use LAG\AdminBundle\Field\Configuration\StringFieldConfiguration;
use LAG\AdminBundle\Tests\AdminTestBase;
use Symfony\Component\OptionsResolver\OptionsResolver;

class LinkConfigurationTest extends AdminTestBase
{
    public function testConfigureOptions()
    {
        $applicationConfiguration = $this->getMockWithoutConstructor(ApplicationConfiguration::class);
        $applicationConfiguration
            ->expects($this->exactly(3))
            ->method('getParameter')
            ->willReturnMap([
                ['string_length', 300],
                ['string_length_truncate', '---'],
                ['fields_template_mapping', [
                    AbstractField::TYPE_LINK => 'link.html.twig',
                ]],
            ])
        ;
    
        $optionsResolver = new OptionsResolver();
        $configuration = new LinkConfiguration();
        $configuration->setApplicationConfiguration($applicationConfiguration);
    
        $configuration->configureOptions($optionsResolver);
    
        $options = $optionsResolver->resolve([
            'route' => 'my_route',
        ]);
    
        $this->assertEquals('my_route', $options['route']);
    }
}
