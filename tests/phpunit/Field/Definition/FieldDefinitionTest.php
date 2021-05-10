<?php

namespace LAG\AdminBundle\Tests\Field\Definition;

use LAG\AdminBundle\Field\Definition\FieldDefinition;
use LAG\AdminBundle\Tests\TestCase;

class FieldDefinitionTest extends TestCase
{
    public function testDefinition(): void
    {
        $definition = new FieldDefinition('string', ['my_options' => true], ['form_options' => true]);

        $this->assertEquals('string', $definition->getType());
        $this->assertEquals(['my_options' => true], $definition->getOptions());
        $this->assertEquals(['form_options' => true], $definition->getFormOptions());
    }
}
