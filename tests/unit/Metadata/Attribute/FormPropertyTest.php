<?php

declare(strict_types=1);

namespace LAG\AdminBundle\Tests\Unit\Metadata\Attribute;

use LAG\AdminBundle\Grid\DataTransformer\FormDataTransformer;
use LAG\AdminBundle\Metadata\Attribute\Form;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Form\Extension\Core\Type\FormType;

final class FormPropertyTest extends TestCase
{
    #[Test]
    public function itReturnsDefaultProperties(): void
    {
        $form = new Form(name: 'actions');

        self::assertSame('actions', $form->getName());
        self::assertSame('@LAGAdmin/grids/properties/form.html.twig', $form->getTemplate());
        self::assertFalse($form->isSortable());
        self::assertSame(FormDataTransformer::class, $form->getDataTransformer());
        self::assertSame(FormType::class, $form->getForm());
        self::assertNull($form->getFormTemplate());
        self::assertSame([], $form->getFormOptions());
        self::assertSame([], $form->getProperties());
    }

    #[Test]
    public function itReturnsImmutableCopiesForWithMethods(): void
    {
        $form = new Form(name: 'actions');

        $new = $form->setForm('App\Form\MyType');
        self::assertNotSame($form, $new);
        self::assertSame('App\Form\MyType', $new->getForm());
        self::assertSame(FormType::class, $form->getForm());

        $new = $form->setFormOptions(['csrf_protection' => false]);
        self::assertNotSame($form, $new);
        self::assertSame(['csrf_protection' => false], $new->getFormOptions());

        $new = $form->withProperties(['field1', 'field2']);
        self::assertNotSame($form, $new);
        self::assertSame(['field1', 'field2'], $new->getProperties());

        $new = $form->withFormTemplate('@App/form.html.twig');
        self::assertNotSame($form, $new);
        self::assertSame('@App/form.html.twig', $new->getFormTemplate());
    }
}
