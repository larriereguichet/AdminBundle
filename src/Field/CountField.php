<?php

namespace LAG\AdminBundle\Field;

use LAG\AdminBundle\Configuration\ActionConfiguration;
use Symfony\Component\OptionsResolver\OptionsResolver;

class CountField extends AbstractField
{
    public function isSortable(): bool
    {
        return false;
    }

    public function configureOptions(OptionsResolver $resolver, ActionConfiguration $configuration)
    {
        $resolver->setDefaults([
            'empty_string' => null,
        ]);
    }

    public function render($value = null): string
    {
        $count = count($value);

        if ($count > 0 || $this->options['empty_string'] === null) {
            $render = $count;
        } else {
            $render = $this->options['empty_string'];
        }

        return $render;
    }
}
