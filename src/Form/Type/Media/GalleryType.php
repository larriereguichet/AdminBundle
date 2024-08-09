<?php

declare(strict_types=1);

namespace LAG\AdminBundle\Form\Type\Media;

use LAG\AdminBundle\Entity\AbstractImage;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\OptionsResolver\OptionsResolver;

class GalleryType extends AbstractType
{
    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver
            ->setDefaults([
                'attr' => [
                    'class' => 'media-gallery-input',
                ],
                'class' => AbstractImage::class,
            ])
        ;
    }

    public function getParent(): string
    {
        return EntityType::class;
    }

    public function getBlockPrefix(): string
    {
        return 'lag_admin_gallery';
    }
}
