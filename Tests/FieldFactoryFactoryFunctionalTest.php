<?php

namespace BlueBear\AdminBundle\Tests;

use BlueBear\AdminBundle\Admin\Configuration\ApplicationConfiguration;
use BlueBear\AdminBundle\Admin\Factory\FieldFactory;
use BlueBear\AdminBundle\Admin\Field;
use BlueBear\AdminBundle\Admin\FieldInterface;
use DateTime;
use Symfony\Component\Routing\RouterInterface;

class FieldFactoryFactoryFunctionalTest extends Base
{
    protected $rendererMapping = [
        'string' => 'bluebear.admin.field.string',
        'array' => 'bluebear.admin.field.array',
        'date' => 'bluebear.admin.field.date',
        'link' => 'bluebear.admin.field.link',
    ];

    public function testCreate()
    {
        $this->initApplication();
        $fieldFactory = new FieldFactory(new ApplicationConfiguration([], 'en'));
        $fieldFactory->setContainer($this->container);

        foreach ($this->rendererMapping as $type => $serviceId) {
            $fieldFactory->addFieldMapping($type, $serviceId);
        }
        $configuration = $this->getFakeFieldConfiguration();

        foreach ($configuration as $fieldName => $fieldConfiguration) {
            $field = $fieldFactory->create($fieldName, $fieldConfiguration);
            $this->doTestFieldForConfiguration($field, $fieldConfiguration, $fieldName);
        }
    }

    protected function doTestFieldForConfiguration(FieldInterface $field, array $configuration, $fieldName)
    {
        $this->initApplication();
        $this->assertEquals($fieldName, $field->getName());

        if (!array_key_exists('type', $configuration)) {
            $configuration['type'] = 'string';
        }
        $this->assertArrayHasKey($field->getType(), $this->rendererMapping);

        if ($configuration['type'] == 'string') {
            $this->assertNotNull($field->render('Test'));

            if (array_key_exists('options', $configuration)) {
                $this->assertEquals('StringTest0123456789', $field->render('StringTest0123456789'));
                $this->assertEquals('StringTestVeryLongTextToSeeTruncationMarkupWi...', $field->render('StringTestVeryLongTextToSeeTruncationMarkupWithCharacters'));
            }
        } else if ($configuration['type'] == 'array') {
            $this->assertEquals('test//other_test', $field->render(['test', 'other_test']));
            $this->assertExceptionRaised('Exception', function () use ($field) {
                $field->render('test');
            });
        } else if ($configuration['type'] == 'date') {
            $this->assertEquals(date('d/m/Y h:i:s'), $field->render(new DateTime()));
        } else if ($configuration['type'] == 'link') {

            if (array_key_exists('url', $configuration['options'])) {
                $url = $configuration['options']['url'];
            } else {
                /** @var RouterInterface $router */
                $router = $this->container->get('routing');
                $url = $router->generate($configuration['route'], $configuration['parameters']);
            }
            $this->assertEquals('<a href="'.$url.'" target="_blank" title="">MyText</a>', $field->render('MyText'));
        }
    }

    protected function getFakeFieldConfiguration()
    {
        return [
            'id' => [],
            'minimal_test' => [],
            'string_test' => [
                'type' => 'string',
                'options' => [
                    'length' => 45
                ]
            ],
            'array_test' => [
                'type' => 'array',
                'options' => [
                    'glue' => '//'
                ]
            ],
            'date_test' => [
                'type' => 'date',
                'options' => [
                    'format' => 'd/m/Y h:i:s'
                ]
            ],
            'link_test' => [
                'type' => 'link',
                'options' => [
                    'target' => '_blank',
                    'url' => 'https://www.google.fr'
                ]
            ]

        ];
    }
}
