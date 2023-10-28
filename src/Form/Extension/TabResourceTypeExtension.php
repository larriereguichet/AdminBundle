<?php

declare(strict_types=1);

namespace LAG\AdminBundle\Form\Extension;

use Symfony\Component\Form\AbstractTypeExtension;
use Symfony\Component\Form\Extension\Core\Type\FormType;
use Symfony\Component\OptionsResolver\OptionsResolver;

class TabResourceTypeExtension extends AbstractTypeExtension
{
    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'tab' => null,
        ]);
    }

    public static function getExtendedTypes(): iterable
    {
        return [FormType::class];
    }
}
