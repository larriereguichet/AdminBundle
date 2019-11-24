<?php

namespace LAG\AdminBundle\Tests\Field;

use LAG\AdminBundle\Configuration\ActionConfiguration;
use LAG\AdminBundle\Field\StringField;
use LAG\AdminBundle\Tests\AdminTestBase;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Contracts\Translation\TranslatorInterface;

class StringFieldTest extends AdminTestBase
{
    public function testRender()
    {
        $resolver = new OptionsResolver();

        $configuration = $this->createMock(ActionConfiguration::class);
        $configuration
            ->expects($this->atLeastOnce())
            ->method('get')
            ->willReturnMap([
                ['string_length', 10],
                ['string_length_truncate', '___'],
            ])
        ;

        $translator = $this->createMock(TranslatorInterface::class);
        $translator
            ->expects($this->atLeastOnce())
            ->method('trans')
            ->with('my_little_key')
            ->willReturn('My long string more than 10 characters !!!')
        ;

        $field = new StringField('my_field');
        $field->setTranslator($translator);
        $field->configureOptions($resolver, $configuration);
        $field->setOptions($resolver->resolve());

        $this->assertTrue($field->isSortable());
        $render = $field->render('my_little_key');
        $this->assertEquals('My long st___', $render);
    }
}
