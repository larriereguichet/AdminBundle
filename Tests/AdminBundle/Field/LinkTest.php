<?php

namespace LAG\AdminBundle\Tests\AdminBundle\Field;

use LAG\AdminBundle\Field\Field\Link;
use LAG\AdminBundle\Tests\AdminTestBase;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Translation\IdentityTranslator;
use Test\TestBundle\Entity\TestEntity;
use Twig_Environment;

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
            'icon' => 'fa-test',
            'template' => 'LAGAdminBundle:Render:link.html.twig',
        ];
        $resolver = new OptionsResolver();
    
        $twig = $this
            ->getMockBuilder(Twig_Environment::class)
            ->disableOriginalConstructor()
            ->getMock()
        ;
        $twig
            ->expects($this->once())
            ->method('render')
            ->with('LAGAdminBundle:Render:link.html.twig', [
                'text' => 'test',
                'route' => 'route_test',
                'parameters' => $options['parameters'],
                'target' => '_blank',
                'url' => '',
                'title' => 'MyTitle',
                'icon' => 'fa-test',
            ])
            ->willReturn('<p>lol man</p>')
        ;
        
        $linkField = new Link();
        $linkField->setApplicationConfiguration($this->createApplicationConfiguration());
        $linkField->configureOptions($resolver);
        $linkField->setOptions($resolver->resolve($options));
        $linkField->setTranslator(new IdentityTranslator());
        $linkField->setTwig($twig);
        $linkField->setEntity(new TestEntity());

        $result = $linkField->render('test');
        $this->assertInternalType('string', $result);
    }
}
