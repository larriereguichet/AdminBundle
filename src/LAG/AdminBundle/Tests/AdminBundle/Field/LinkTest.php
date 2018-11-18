<?php

namespace LAG\AdminBundle\Tests\AdminBundle\Field;

use LAG\AdminBundle\Field\Field\Link;
use LAG\AdminBundle\Tests\AdminTestBase;
use Twig_Environment;

class LinkTest extends AdminTestBase
{
    public function testRender()
    {
        $twig = $this->getMockWithoutConstructor(Twig_Environment::class);
        $twig
            ->expects($this->once())
            ->method('render')
            ->with('link.html.twig', [
                'text' => 'a link text',
                'parameters' => [],
                'options' => [
                    'text' => null,
                    'translation' => null,
                    'length' => null,
                    'replace' => null,
                    'parameters' => [],
                    'template' => 'link.html.twig',
                ],
            ])
            ->willReturn('html content')
        ;
        
        $linkField = new Link('my-field');
        $linkField->setTwig($twig);
    
        $this->setPrivateProperty($linkField, 'options', [
            'text' => null,
            'translation' => null,
            'length' => null,
            'replace' => null,
            'parameters' => [],
            'template' => 'link.html.twig',
        ]);
        
        $content = $linkField->render('a link text');
    
        $this->assertEquals('html content', $content);
    }
}
