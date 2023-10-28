<?php

namespace LAG\AdminBundle\Form\Type\Image;

use LAG\AdminBundle\Form\Type\Media\GalleryType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\DataTransformerInterface;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Vich\UploaderBundle\Form\Type\VichImageType;

class ImageChoiceType extends AbstractType
{
    public function __construct(private DataTransformerInterface $transformer)
    {
    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('upload', VichImageType::class, [
                'label' => 'jk_media.image.upload_from_computer',
                'translation_domain' => 'media',
                'required' => false,
                'allow_delete' => true,
            ])
            ->add('gallery', GalleryType::class, [
                'required' => false,
            ])
            ->addModelTransformer($this->transformer)
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'label' => false,
        ]);
    }

    public function getBlockPrefix(): string
    {
        return 'lag_admin_image_choice';
    }
}
