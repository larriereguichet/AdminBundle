<?php

namespace LAG\AdminBundle\Tests\Field;

use Doctrine\Common\Collections\ArrayCollection;
use Iterator;
use LAG\AdminBundle\Field\ArrayField;
use LAG\AdminBundle\Field\View\TextView;

class ArrayFieldTest extends FieldTestCase
{
    public function testRender(): void
    {
        $field = $this->factory->create('myField', [
            'type' => 'array',
        ]);
        $this->assertEquals([
            'attr' => [
                'class' => 'admin-field admin-field-array',
            ],
            'header_attr' => [
                'class' => 'admin-header admin-header-array',
            ],
            'label' => null,
            'mapped' => false,
            'property_path' => 'myField',
            'template' => '@LAGAdmin/fields/auto.html.twig',
            'translation' => false,
            'translation_domain' => 'admin',
            'sortable' => false,
            'glue' => ', ',
        ], $field->getOptions());
        $this->assertInstanceOf(ArrayField::class, $field);

        $view = $field->createView();
        $this->assertInstanceOf(TextView::class, $view);

        $transformer = $field->getDataTransformer();
        $this->assertEquals('', $transformer(null));
        $this->assertEquals('My, Little, Panda', $transformer(new ArrayCollection(['My', 'Little', 'Panda'])));
        $this->assertEquals('Panda', $transformer($this->getIterator()));
    }

    private function getIterator(): Iterator
    {
        yield 'Panda';
    }
}
