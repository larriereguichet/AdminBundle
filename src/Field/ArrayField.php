<?php

namespace LAG\AdminBundle\Field;

use LAG\AdminBundle\Configuration\ActionConfiguration;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ArrayField extends AbstractField
{
    public function configureOptions(OptionsResolver $resolver, ActionConfiguration $actionConfiguration)
    {
        $resolver
            ->setDefaults([
                'glue' => ',',
            ])
            ->setAllowedTypes('glue', 'string')
        ;
    }

    public function isSortable(): bool
    {
        return false;
    }

    public function render($value = null): string
    {
        if (null === $value) {
            return '';
        }

        return implode($this->options['glue'], $value);
    }
}
