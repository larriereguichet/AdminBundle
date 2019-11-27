<?php

namespace LAG\AdminBundle\Configuration;

use JK\Configuration\Configuration;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\OptionsResolver\OptionsResolver;

class FilterConfiguration extends Configuration
{
    /**
     * Define allowed parameters and values for this configuration, using optionsResolver component.
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver
            ->setDefaults([
                'type' => TextType::class,
                'options' => [],
                'comparator' => 'like',
                'operator' => 'or',
            ])
            ->setRequired('name')
            ->setAllowedTypes('name', 'string')
            ->setAllowedTypes('type', 'string')
            ->setAllowedTypes('options', 'array')
            ->setAllowedTypes('comparator', 'string')
            ->setAllowedTypes('operator', 'string')
            ->setAllowedValues('operator', [
                'and',
                'or',
            ])
        ;
    }
}
