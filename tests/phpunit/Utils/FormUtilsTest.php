<?php

namespace LAG\AdminBundle\Tests\Utils;

use LAG\AdminBundle\Form\Type\DateRangeType;
use LAG\AdminBundle\Form\Type\Select2\Select2EntityType;
use LAG\AdminBundle\Form\Type\Select2\Select2Type;
use LAG\AdminBundle\Tests\TestCase;
use LAG\AdminBundle\Utils\FormUtils;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;

class FormUtilsTest extends TestCase
{
    public function testConvertShortFormType()
    {
        $this->assertEquals(Select2Type::class, FormUtils::convertShortFormType('choice'));
        $this->assertEquals(TextareaType::class, FormUtils::convertShortFormType('array'));
        $this->assertEquals(TextType::class, FormUtils::convertShortFormType('string'));
        $this->assertEquals(Select2EntityType::class, FormUtils::convertShortFormType('entity'));
        $this->assertEquals(DateRangeType::class, FormUtils::convertShortFormType('date'));
        $this->assertEquals(DateRangeType::class, FormUtils::convertShortFormType('datetime'));
        $this->assertEquals(TextareaType::class, FormUtils::convertShortFormType('text'));
        $this->assertEquals(NumberType::class, FormUtils::convertShortFormType('number'));
        $this->assertEquals(IntegerType::class, FormUtils::convertShortFormType('integer'));
        $this->assertEquals(IntegerType::class, FormUtils::convertShortFormType('smallint'));
        $this->assertEquals(CheckboxType::class, FormUtils::convertShortFormType('boolean'));
        $this->assertEquals('my_custom_type', FormUtils::convertShortFormType('my_custom_type'));
    }
}
