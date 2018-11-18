<?php

namespace LAG\AdminBundle\Tests\Form\Type;

use LAG\AdminBundle\Form\Type\DeleteType;
use LAG\AdminBundle\Tests\AdminTestBase;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class DeleteTypeTest extends AdminTestBase
{
    public function testConfigureOptions()
    {
        $resolver = new OptionsResolver();
        $type = new DeleteType();

        $type->configureOptions($resolver);
        $options = $resolver->resolve();

        $this->assertArrayHasKey('label', $options);
        $this->assertFalse($options['label']);
    }

    public function testBuildForm()
    {
        $builder = $this->createMock(FormBuilderInterface::class);
        $builder
            ->expects($this->atLeastOnce())
            ->method('add')
            ->with('id', HiddenType::class)
        ;
        $type = new DeleteType();

        $type->buildForm($builder, []);
    }
}
