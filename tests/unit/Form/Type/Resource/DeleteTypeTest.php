<?php

declare(strict_types=1);

namespace LAG\AdminBundle\Tests\Form\Type\Resource;

use LAG\AdminBundle\Form\Type\Resource\DeleteType;
use LAG\AdminBundle\Metadata\Resource;
use LAG\AdminBundle\Tests\TestCase;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class DeleteTypeTest extends TestCase
{
    public function testConfigureOptions(): void
    {
        $resolver = new OptionsResolver();
        $type = new DeleteType();
        $resource = new Resource(identifiers: ['id', 'slug']);

        $type->configureOptions($resolver);
        $options = $resolver->resolve(['resource' => $resource]);

        $this->assertArrayHasKey('label', $options);
        $this->assertFalse($options['label']);
        $this->assertEquals($resource, $options['resource']);
    }

    public function testBuildForm(): void
    {
        $builder = $this->createMock(FormBuilderInterface::class);
        $builder
            ->expects(self::exactly(2))
            ->method('add')
            ->willReturnCallback(function (string $name, string $type) use ($builder) {
                $this->assertContains($name, ['id', 'slug']);
                $this->assertEquals(HiddenType::class, $type);

                return $builder;
            })
        ;
        $type = new DeleteType();

        $type->buildForm($builder, ['resource' => new Resource(identifiers: ['id', 'slug'])]);
    }
}
