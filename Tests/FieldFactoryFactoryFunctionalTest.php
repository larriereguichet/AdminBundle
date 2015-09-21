<?php

namespace BlueBear\AdminBundle\Tests;


use BlueBear\AdminBundle\Admin\Configuration\ApplicationConfiguration;
use BlueBear\AdminBundle\Admin\Factory\FieldFactory;
use BlueBear\AdminBundle\Admin\Factory\FieldRendererFactory;
use BlueBear\AdminBundle\Admin\Field;
use DateTime;
use Twig_Environment;
use Twig_Loader_Filesystem;

class FieldFactoryFactoryFunctionalTest extends Base
{
    protected $rendererMapping = [
        'string' => 'BlueBear\AdminBundle\Admin\Render\StringRenderer',
        'array' => 'BlueBear\AdminBundle\Admin\Render\ArrayRenderer',
        'date' => 'BlueBear\AdminBundle\Admin\Render\DateRenderer',
        'link' => 'BlueBear\AdminBundle\Admin\Render\LinkRenderer',
    ];

    public function testCreate()
    {
        $fieldRendererFactory = new FieldRendererFactory($this->rendererMapping, new Twig_Environment(), new ApplicationConfiguration([], 'en'));
        $fieldFactory = new FieldFactory($fieldRendererFactory);
        $configuration = $this->getFakeFieldConfiguration();

        foreach ($configuration as $fieldName => $fieldConfiguration) {
            $field = $fieldFactory->create($fieldName, $fieldConfiguration);
            $this->doTestFieldForConfiguration($field, $fieldConfiguration, $fieldName);
        }
    }

    protected function doTestFieldForConfiguration(Field $field, array $configuration, $fieldName)
    {
        $this->assertEquals($fieldName, $field->getName());

        if (!array_key_exists('type', $configuration)) {
            $configuration['type'] = 'string';
        }
        $renderer = $field->getRenderer();

        $this->assertArrayHasKey($configuration['type'], $this->rendererMapping);
        $this->assertInstanceOf($this->rendererMapping[$configuration['type']], $renderer);
        $this->assertInstanceOf('BlueBear\AdminBundle\Admin\Render\RendererInterface', $renderer);

//        if (in_array('BlueBear\AdminBundle\Admin\Render\TwigRendererInterface', class_implements($renderer))) {
//            $this->assertTrue(method_exists($renderer, 'setTwig'));
//            $twig = new Twig_Environment(new Twig_Loader_Filesystem([
//                '__main__' => __DIR__ . '/../'
//            ]));
//            $renderer->setTwig($twig);
//        }
        if ($configuration['type'] == 'string') {
            $this->assertNotNull($renderer->render('Test'));

            if (array_key_exists('options', $configuration)) {
                $this->assertEquals('StringTest0123456789', $renderer->render('StringTest0123456789'));
                $this->assertEquals('StringTestVeryLongTextToSeeTruncationMarkupWi...', $renderer->render('StringTestVeryLongTextToSeeTruncationMarkupWithCharacters'));
            }
        } else if ($configuration['type'] == 'array') {
            $this->assertEquals('test//other_test', $renderer->render(['test', 'other_test']));
            $this->assertExceptionRaised('Exception', function () use ($renderer) {
                $renderer->render('test');
            });
        } else if ($configuration['type'] == 'date') {
            $this->assertEquals(date('d/m/Y h:i:s'), $renderer->render(new DateTime()));
        } else if ($configuration['type'] == 'link') {
            //$this->assertEquals('<a></a>', $renderer->render('MyText'));
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
                    'target' => '_blank'
                ]
            ]

        ];
    }
}
