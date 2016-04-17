<?php

namespace Tests\AdminBundle\Field;

use LAG\AdminBundle\Field\Field\Link;
use LAG\AdminBundle\Tests\AdminTestBase;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Translation\IdentityTranslator;
use Test\TestBundle\Entity\TestEntity;

class LinkTest extends AdminTestBase
{
    public function testRender()
    {
        $options = [
            'route' => 'route_test',
            'parameters' => [
                'id' => null
            ],
            'target' => '_blank',
            'title' => 'MyTitle',
            'icon' => 'fa-test'
        ];
        $resolver = new OptionsResolver();

        $linkField = new Link();
        $linkField->setApplicationConfiguration($this->createApplicationConfiguration());
        $linkField->configureOptions($resolver);
        $linkField->setOptions($resolver->resolve($options));
        $linkField->setTranslator(new IdentityTranslator());
        $linkField->setTwig($this->createTwigEnvironment());
        $linkField->setEntity(new TestEntity());

        $result = $linkField->render('test');

        $this->assertEquals('LAGAdminBundle:Render:link.html.twig', $result['template']);
        $this->assertEquals('test', $result['parameters']['text']);
        $this->assertEquals('route_test', $result['parameters']['route']);
        $this->assertEquals($options['parameters'], $result['parameters']['parameters']);
        $this->assertEquals('_blank', $result['parameters']['target']);
        $this->assertEquals('MyTitle', $result['parameters']['title']);
        $this->assertEquals('fa-test', $result['parameters']['icon']);
    }
}
