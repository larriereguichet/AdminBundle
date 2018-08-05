<?php

namespace LAG\AdminBundle\Field;

use LAG\AdminBundle\Configuration\ActionConfiguration;
use Symfony\Component\OptionsResolver\OptionsResolver;

class MappedField extends AbstractField
{
    public function isSortable(): bool
    {
        return false;
    }

    public function configureOptions(OptionsResolver $resolver, ActionConfiguration $actionConfiguration)
    {
        $resolver
            ->setRequired('map')
            ->setAllowedTypes('map', 'array')
        ;
    }

    public function render($value = null): string
    {
        if (key_exists($value, $this->options['map'])) {
            $value = $this->options['map'][$value];
        }

        return (string)$value;
    }
}
