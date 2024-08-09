<?php

declare(strict_types=1);

namespace LAG\AdminBundle\Form\Type\Image;

use LAG\AdminBundle\Entity\ImageInterface;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;
use Symfony\Component\OptionsResolver\OptionsResolver;

final class ImageType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder->add('file', FileType::class);
    }

    public function buildView(FormView $view, FormInterface $form, array $options): void
    {
        $view->vars['image_filter'] = $options['image_filter'];
        $view->vars['image_full_filter'] = $options['image_full_filter'];
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver
            ->setDefaults(['data_class' => ImageInterface::class])

            ->define('image_filter')
            ->default('lag_admin_thumbnail')
            ->allowedTypes('string')

            ->define('image_full_filter')
            ->default('lag_admin_full')
            ->allowedTypes('string')
        ;
    }

    public function getBlockPrefix(): string
    {
        return 'lag_admin_image';
    }
}
