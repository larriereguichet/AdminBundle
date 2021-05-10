<?php

namespace LAG\AdminBundle\Tests\Field;

use LAG\AdminBundle\Field\StringField;

class StringFieldTest extends FieldTestCase
{
    public function testField(): void
    {
        $field = $this->factory->create('name', ['type' => 'string']);

        $this->assertEquals([
            'length' => 200,
            'replace' => '...',
            'translate_title' => true,
            'attr' => ['class' => 'admin-field admin-field-string'],
            'header_attr' => ['class' => 'admin-header admin-header-string'],
            'label' => null,
            'mapped' => false,
            'property_path' => 'name',
            'template' => '@LAGAdmin/fields/string.html.twig',
            'translation' => false,
            'translation_domain' => null,
            'sortable' => true,
        ], $field->getOptions());
        $this->assertInstanceOf(StringField::class, $field);
    }
}
