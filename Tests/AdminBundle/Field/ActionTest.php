<?php

namespace LAG\AdminBundle\Tests\AdminBundle\Field;

use LAG\AdminBundle\Field\Field\Action;
use LAG\AdminBundle\Tests\AdminTestBase;
use Symfony\Component\OptionsResolver\Exception\InvalidOptionsException;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Translation\IdentityTranslator;
use Twig_Environment;

class ActionTest extends AdminTestBase
{
    public function testRender()
    {
        $options = [];
        $resolver = new OptionsResolver();
        
        $linkField = new Action();
        $linkField->setApplicationConfiguration($this->createApplicationConfiguration());
        $linkField->configureOptions($resolver);
        
        // an url or a route SHOULD be provided
        $this->assertExceptionRaised(InvalidOptionsException::class, function() use ($linkField, $resolver, $options) {
            $linkField->setOptions($resolver->resolve($options));
        });
        
        $options = [
            'url' => 'http:/test.fr/',
            'title' => 'MyAction'
        ];
        
        $twig = $this
            ->getMockBuilder(Twig_Environment::class)
            ->disableOriginalConstructor()
            ->getMock()
        ;
        $twig
            ->expects($this->once())
            ->method('render')
            ->with('LAGAdminBundle:Render:link.html.twig', [
                'text' => 'MyAction',
                'url' => $options['url'],
                'route' => '',
                'parameters' => [],
                'target' => '_self',
                'title' => 'MyAction',
                'icon' => '',
            ])
            ->willReturn('<p>a string yeah !!! </p>')
        ;
        
        $linkField->setOptions($resolver->resolve($options));
        $linkField->setTranslator(new IdentityTranslator());
        $linkField->setTwig($twig);
        
        $result = $linkField->render('test');
        $this->assertInternalType('string', $result);
    }
}
