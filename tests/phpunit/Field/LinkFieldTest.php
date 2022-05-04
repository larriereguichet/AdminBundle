<?php

namespace LAG\AdminBundle\Tests\Field;

use LAG\AdminBundle\Application\Configuration\ApplicationConfiguration;
use LAG\AdminBundle\Field\LinkField;
use LAG\AdminBundle\Tests\TestCase;
use Symfony\Component\OptionsResolver\OptionsResolver;

class LinkFieldTest extends TestCase
{
    private LinkField $field;

    public function testDefaultOptions(): void
    {
        $resolver = new OptionsResolver();
        $applicationConfiguration = new ApplicationConfiguration(['resources_path' => 'test']);

        $this->field->setApplicationConfiguration($applicationConfiguration);
        $this->field->configureOptions($resolver);
        $this->field->setOptions($resolver->resolve([
            'url' => '/test',
        ]));

        $this->assertEquals([
            'admin' => null,
            'action' => null,
            'default' => '~',
            'icon' => null,
            'mapped' => true,
            'route' => null,
            'route_parameters' => [],
            'target' => '_self',
            'template' => '@LAGAdmin/fields/link.html.twig',
            'text' => '',
            'title' => null,
            'url' => '/test',
        ], $this->field->getOptions());
    }

    public function testOptions(): void
    {
        $resolver = new OptionsResolver();
        $applicationConfiguration = new ApplicationConfiguration(['resources_path' => 'test']);

        $this->field->setApplicationConfiguration($applicationConfiguration);
        $this->field->configureOptions($resolver);
        $this->field->setOptions($resolver->resolve([
            'url' => '/test',
        ]));

        $this->assertEquals([
            'admin' => null,
            'action' => null,
            'default' => '~',
            'icon' => null,
            'mapped' => true,
            'route' => null,
            'route_parameters' => [],
            'target' => '_self',
            'template' => '@LAGAdmin/fields/link.html.twig',
            'text' => '',
            'title' => null,
            'url' => '/test',
        ], $this->field->getOptions());
    }

    protected function setUp(): void
    {
        $this->field = new LinkField('my_field', 'link');
    }
}
