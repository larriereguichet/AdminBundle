<?php

namespace LAG\AdminBundle\Field;

use LAG\AdminBundle\Configuration\ActionConfiguration;
use Symfony\Component\OptionsResolver\Options;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Display a link with button render and options.
 */
class ActionField extends LinkField
{
    public function isSortable(): bool
    {
        return true;
    }

    public function configureOptions(OptionsResolver $resolver, ActionConfiguration $actionConfiguration)
    {
        parent::configureOptions($resolver, $actionConfiguration);

        $resolver
            ->setDefault('class', '')
            ->setNormalizer('class', function (Options $options, $value) {
                if ($value) {
                    return $value;
                }
                $action = null;

                if ($options->offsetGet('action')) {
                    $action = $options->offsetGet('action');
                }

                if ('edit' === $action) {
                    return 'btn btn-primary';
                }

                if ('delete' === $action) {
                    return 'btn btn-danger';
                }

                return 'btn btn-secondary';
            })
        ;
    }
}
