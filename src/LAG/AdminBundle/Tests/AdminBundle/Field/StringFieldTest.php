<?php

namespace LAG\AdminBundle\Tests\AdminBundle\Field;

use LAG\AdminBundle\Field\Configuration\StringFieldConfiguration;
use LAG\AdminBundle\Field\Field\StringField;
use LAG\AdminBundle\Tests\AdminTestBase;
use Symfony\Component\Translation\TranslatorInterface;

class StringFieldTest extends AdminTestBase
{
    public function testRender()
    {
        $configuration = $this->getMockWithoutConstructor(StringFieldConfiguration::class);
        $configuration
            ->expects($this->once())
            ->method('isResolved')
            ->willReturn(true)
        ;
        $configuration
            ->expects($this->once())
            ->method('getParameters')
            ->willReturn([
                'translation' => true,
                'length' => 16,
                'replace' => '...',
            ])
        ;
        $translator = $this->getMockWithoutConstructor(TranslatorInterface::class);
        $translator
            ->expects($this->once())
            ->method('trans')
            ->with('What a beautiful sentence')
            ->willReturn('What a beautiful translation')
        ;

        $field = new StringField('my-field');
        $field->setConfiguration($configuration);
        $field->setTranslator($translator);

        $content = $field->render('What a beautiful sentence');

        $this->assertEquals('What a beautiful...', $content);
    }
}
