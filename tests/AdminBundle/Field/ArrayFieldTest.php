<?php

namespace LAG\AdminBundle\Tests\Field;

use LAG\AdminBundle\Configuration\ActionConfiguration;
use LAG\AdminBundle\Field\ArrayField;
use LAG\AdminBundle\Tests\AdminTestBase;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ArrayFieldTest extends AdminTestBase
{
    public function testRender()
    {
        $resolver = new OptionsResolver();
        $configuration = $this->createMock(ActionConfiguration::class);

        $field = new ArrayField('my_field');
        $field->configureOptions($resolver, $configuration);
        $options = $resolver->resolve([
            'glue' => ' ',
        ]);
        $field->setOptions($options);

        $render = $field->render([
            'My Field',
            'is a',
            'panda',
        ]);
        $this->assertEquals('My Field is a panda', $render);
        $this->assertEquals('', $field->render(null));
        $this->assertFalse($field->isSortable());
    }
}
