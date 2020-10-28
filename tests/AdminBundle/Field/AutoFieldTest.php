<?php

namespace LAG\AdminBundle\Tests\Field;

use DateTime;
use Doctrine\Common\Collections\ArrayCollection;
use LAG\AdminBundle\Field\AutoField;
use LAG\AdminBundle\Tests\Fixtures\FakeEntity;

class AutoFieldTest extends FieldTestCase
{
    public function testRender()
    {
        $field = $this->factory->create('auto', []);

        $this->assertInstanceOf(AutoField::class, $field);
        $this->assertEquals([
            'attr' => ['class' => 'admin-field admin-field-auto'],
            'header_attr' => ['class' => 'admin-header admin-header-auto'],
            'label' => null,
            'mapped' => false,
            'property_path' => 'auto',
            'template' => '@LAGAdmin/fields/auto.html.twig',
            'translation' => false,
            'translation_domain' => null,
            'sortable' => true,
            'date_format' => 'd/m/Y',
        ], $field->getOptions());

        $transformer = $field->getDataTransformer();

        $this->assertEquals('', $transformer(null));
        $this->assertEquals('MyData', $transformer('MyData'));
        $this->assertEquals('1', $transformer(1));
        $this->assertEquals('My,Little,Panda', $transformer(['My', 'Little', 'Panda']));

        $now = new DateTime();
        $this->assertEquals($now->format('d/m/Y'), $transformer($now));

        $data = new FakeEntity(666, 'panda');
        $this->assertEquals('panda', $transformer($data));

        $data = new ArrayCollection(['My', 'Little', 'Panda']);
        $this->assertEquals('My,Little,Panda', $transformer($data));

        $data = new \stdClass();
        $this->assertEquals('', $transformer($data));
    }
}
