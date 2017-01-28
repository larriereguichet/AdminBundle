<?php

namespace LAG\AdminBundle\Tests\AdminBundle\Field;

use DateTime;
use LAG\AdminBundle\Field\Field\Date;
use LAG\AdminBundle\Tests\AdminTestBase;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Translation\IdentityTranslator;

class DateTest extends AdminTestBase
{
    public function testRender()
    {
        $options = [
            'format' => 'd/m/Y',
        ];
        $resolver = new OptionsResolver();
    
        $twig = $this
            ->getMockBuilder(\Twig_Environment::class)
            ->disableOriginalConstructor()
            ->getMock()
        ;
        
        $linkField = new Date();
        $linkField->setApplicationConfiguration($this->createApplicationConfiguration());
        $linkField->configureOptions($resolver);
        $linkField->setOptions($resolver->resolve($options));
        $linkField->setTranslator(new IdentityTranslator());
        $linkField->setTwig($twig);

        $now = new DateTime();

        $result = $linkField->render($now);

        $this->assertEquals($now->format($options['format']), $result);
        $this->assertEquals('test', $linkField->render('test'));
    }
}
