<?php

namespace LAG\AdminBundle\Form\Type\Media;

use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\OptionsResolver\OptionsResolver;

class GalleryType extends AbstractType
{
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver
            ->setDefaults([
                'attr' => [
                    'class' => 'media-gallery-input',
                ],
                'multiple' => false,
            ])
            ->setAllowedTypes('multiple', 'boolean')
        ;
    }

    public function getParent(): string
    {
        return EntityType::class;
    }
}
