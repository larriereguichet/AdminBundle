<?php

namespace LAG\AdminBundle\Form\Type\Image;

use LAG\AdminBundle\Entity\Image;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\CallbackTransformer;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ImageType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('imageFile', ImageChoiceType::class)
            ->addModelTransformer(new CallbackTransformer(function ($value) {
                if ($value === null) {
                    $value = new Image();
                }

                return $value;
            }, function ($value) {
                if ($value === null) {
                    $value = new Image();
                }

                return $value;
            }))
        ;
    }

    public function buildView(FormView $view, FormInterface $form, array $options): void
    {
        $view->vars['image_property'] = $options['image_property'];
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver
            ->setDefaults([
                'data_class' => Image::class,
            ])
            ->define('image_property')
            ->default('imageFile')
            ->allowedTypes('string')
        ;

    }

    public function getBlockPrefix(): string
    {
        return 'lag_admin_image';
    }
}
