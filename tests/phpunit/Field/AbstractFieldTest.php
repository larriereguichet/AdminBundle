<?php

namespace LAG\AdminBundle\Tests\Field;

use Exception;
use LAG\AdminBundle\Tests\Fixtures\FakeField;
use Symfony\Component\OptionsResolver\OptionsResolver;

class AbstractFieldTest extends FieldTestCase
{
    public function testSetOptionsWithFrozenField(): void
    {
        $field = $this->factory->create('title', ['type' => 'auto']);
        $this->expectException(Exception::class);
        $field->setOptions([]);
    }

    public function testGetOptionWithMissingOptions(): void
    {
        $field = $this->factory->create('title', ['type' => 'auto']);

        $this->expectException(Exception::class);
        $field->getOption('missing_panda');
    }

    public function testConfigureOptions(): void
    {
        $field = new FakeField('panda', 'fake');

        $resolver = new OptionsResolver();
        $field->configureOptions($resolver);
        $this->assertEquals([], $resolver->resolve());
    }
}
