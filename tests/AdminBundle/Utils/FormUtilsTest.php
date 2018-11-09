<?php

namespace LAG\AdminBundle\Tests\Utils;

use LAG\AdminBundle\Tests\AdminTestBase;
use LAG\AdminBundle\Utils\FormUtils;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\TextType;

class FormUtilsTest extends AdminTestBase
{
    public function testConvertShortFormType()
    {
        $this->assertEquals(ChoiceType::class, FormUtils::convertShortFormType('choice'));
        $this->assertEquals(ChoiceType::class, FormUtils::convertShortFormType('array'));
        $this->assertEquals(TextType::class, FormUtils::convertShortFormType('string'));
        $this->assertEquals(EntityType::class, FormUtils::convertShortFormType('entity'));
        $this->assertEquals(DateType::class, FormUtils::convertShortFormType('date'));
        $this->assertEquals(DateType::class, FormUtils::convertShortFormType('datetime'));
        $this->assertEquals(TextType::class, FormUtils::convertShortFormType('text'));
        $this->assertEquals(NumberType::class, FormUtils::convertShortFormType('number'));
        $this->assertEquals(NumberType::class, FormUtils::convertShortFormType('integer'));
        $this->assertEquals(NumberType::class, FormUtils::convertShortFormType('smallint'));
        $this->assertEquals(CheckboxType::class, FormUtils::convertShortFormType('boolean'));
        $this->assertEquals('my_custom_type', FormUtils::convertShortFormType('my_custom_type'));
    }
}
