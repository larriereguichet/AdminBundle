<?php

namespace LAG\AdminBundle\Tests\AdminBundle\Field;

use LAG\AdminBundle\Field\Field\Action;
use LAG\AdminBundle\Tests\AdminTestBase;

class ActionTest extends AdminTestBase
{
    public function testRender()
    {
        $twig = $this->getMockWithoutConstructor(\Twig_Environment::class);
        $twig
            ->expects($this->once())
            ->method('render')
            ->with('test.html.twig', [
                'text' => 'Some Title',
                'parameters' => [],
                'options' => [
                    'length' => null,
                    'parameters' => [],
                    'replace' => null,
                    'template' => 'test.html.twig',
                    'text' => null,
                    'translation' => false,
                    'title' => 'Some Title',
                ],
            ])
            ->willReturn('html content')
        ;
        
        $linkField = new Action('my-field');
        $linkField->setTwig($twig);
        
        $this->setPrivateProperty($linkField, 'options', [
            'length' => null,
            'parameters' => [],
            'replace' => null,
            'template' => 'test.html.twig',
            'text' => null,
            'translation' => false,
            'title' => 'Some Title',
        ]);
    
        $content = $linkField->render('a value');
        
        $this->assertEquals('html content', $content);
    }
}
