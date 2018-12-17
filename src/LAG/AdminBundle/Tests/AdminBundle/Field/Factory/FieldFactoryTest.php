<?php

namespace LAG\AdminBundle\Tests\AdminBundle\Field\Factory;

use LAG\AdminBundle\Action\Configuration\ActionConfiguration;
use LAG\AdminBundle\Application\Configuration\ApplicationConfiguration;
use LAG\AdminBundle\Application\Configuration\ApplicationConfigurationStorage;
use LAG\AdminBundle\Field\Configuration\StringFieldConfiguration;
use LAG\AdminBundle\Field\Factory\ConfigurationFactory;
use LAG\AdminBundle\Field\Factory\FieldFactory;
use LAG\AdminBundle\Field\Field\StringField;
use LAG\AdminBundle\Tests\AdminTestBase;
use Symfony\Component\Translation\TranslatorInterface;

class FieldFactoryTest extends AdminTestBase
{
    public function testCreate()
    {
        $applicationConfiguration = $this->getMockWithoutConstructor(ApplicationConfiguration::class);
        $applicationConfiguration
            ->expects($this->once())
            ->method('getParameter')
            ->with('fields_mapping')
            ->willReturn([
                'string' => StringField::class,
                'test' => 'lol',
            ])
        ;
        $actionConfiguration = $this->getMockWithoutConstructor(ActionConfiguration::class);

        $fieldConfiguration = $this->getMockWithoutConstructor(StringFieldConfiguration::class);
        $fieldConfiguration
            ->expects($this->once())
            ->method('isResolved')
            ->willReturn(true)
        ;

        $storage = $this->getMockWithoutConstructor(ApplicationConfigurationStorage::class);

        $storage
            ->expects($this->once())
            ->method('getApplicationConfiguration')
            ->willReturn($applicationConfiguration)
        ;

        $configurationFactory = $this->getMockWithoutConstructor(ConfigurationFactory::class);
        $configurationFactory
            ->expects($this->once())
            ->method('create')
            ->willReturnCallback(function ($field, $configuration) use ($actionConfiguration, $fieldConfiguration) {
                $this->assertInstanceOf(StringField::class, $field);
                $this->assertEquals([
                    'length' => 200,
                ], $configuration);

                return $fieldConfiguration;
            })
        ;

        $translator = $this->getMockWithoutConstructor(TranslatorInterface::class);
        $twig = $this->getMockWithoutConstructor(\Twig_Environment::class);

        $fieldFactory = new FieldFactory($storage, $configurationFactory, $translator, $twig);
        $fieldFactory->create('test', [
            'options' => [
                'length' => 200,
            ],
        ], $actionConfiguration);
    }
}
