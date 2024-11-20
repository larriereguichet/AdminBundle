<?php

declare(strict_types=1);

namespace LAG\AdminBundle\Form\Type\Text;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\CallbackTransformer;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;
use Symfony\Component\OptionsResolver\OptionsResolver;

final class TextareaType extends AbstractType
{
    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver
            ->define('toolbar')
            ->allowedTypes('array', 'boolean')
            ->default([
                [['header' => [1, 2, false]]],
                ['bold', 'italic', 'underline', 'strike'],
                ['blockquote', 'code-block'],
                ['link', 'image', 'video'],
            ])
        ;
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->addModelTransformer(new CallbackTransformer(function ($value) {
            if (!json_validate($value)) {
                return json_encode([['insert' => $value]]);
            }

            return $value;
        }, fn ($value) => $value));
    }

    public function buildView(FormView $view, FormInterface $form, array $options): void
    {
        $view->vars['toolbar'] = json_encode($options['toolbar']);
    }

    public function getParent(): string
    {
        return \Symfony\Component\Form\Extension\Core\Type\TextareaType::class;
    }

    public function getBlockPrefix(): string
    {
        return 'lag_admin_textarea';
    }
}
