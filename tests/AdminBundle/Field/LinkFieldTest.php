<?php

namespace AdminBundle\Field;

use LAG\AdminBundle\Configuration\ApplicationConfiguration;
use LAG\AdminBundle\Field\LinkField;
use LAG\AdminBundle\Tests\TestCase;
use Symfony\Component\OptionsResolver\OptionsResolver;

class LinkFieldTest extends TestCase
{
    private LinkField $field;

    public function testDefaultOptions(): void
    {
        $resolver = new OptionsResolver();
        $appConfig = new ApplicationConfiguration(['resources_path' => 'test']);

        $this->field->setApplicationConfiguration($appConfig);
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
        $appConfig = new ApplicationConfiguration(['resources_path' => 'test']);

        $this->field->setApplicationConfiguration($appConfig);
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
