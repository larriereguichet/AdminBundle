<?php

namespace Tests\AdminBundle\Field;

use LAG\AdminBundle\Field\Field\Action;
use LAG\AdminBundle\Tests\AdminTestBase;
use Symfony\Component\OptionsResolver\Exception\InvalidOptionsException;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Translation\IdentityTranslator;

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
            'url' => 'http:/test.fr/'
        ];

        $linkField->setOptions($resolver->resolve($options));
        $linkField->setTranslator(new IdentityTranslator());
        $linkField->setTwig($this->createTwigEnvironment());

        $result = $linkField->render('test');

        $this->assertEquals('LAGAdminBundle:Render:link.html.twig', $result['template']);
        $this->assertEquals($options['url'], $result['parameters']['url']);
    }
}
