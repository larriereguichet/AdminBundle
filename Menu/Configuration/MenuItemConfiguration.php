<?php

namespace LAG\AdminBundle\Menu\Configuration;

use LAG\AdminBundle\Configuration\Configuration;
use Symfony\Component\OptionsResolver\Exception\InvalidOptionsException;
use Symfony\Component\OptionsResolver\Options;
use Symfony\Component\OptionsResolver\OptionsResolver;

class MenuItemConfiguration extends Configuration
{
    /**
     * Define allowed parameters and values for this configuration, using optionsResolver component.
     *
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        // user can defined an admin name
        $resolver
            ->setDefault('admin', null)
            ->setNormalizer('admin', function(Options $options, $adminName) {

                // user has to defined either an admin name and an action name, or a route name with optional
                // parameters, or an url
                if ($adminName === null
                    && $options->offsetGet('route') === null
                    && $options->offsetGet('url') === null
                ) {

                    throw new InvalidOptionsException(
                        'You should either defined an admin name, or route name or an uri'
                    );
                }

                return $adminName;
            });

        // if an admin name is set, an action name can provided. This action will be the menu link
        $resolver
            ->setDefault('action', null)
            ->setNormalizer('action', function(Options $options, $action) {

                // if an action name is provided, an admin name should be defined too
                if ($action !== null && $options->offsetGet('admin') === null) {
                    throw new InvalidOptionsException(
                        'You should provide an admin name for this action '.$action
                    );
                }

                // default to list action
                if ($options->offsetGet('admin') !== null && $action === null) {
                    $action = 'list';
                }

                return $action;
            });

        // a route can also be provided
        $resolver
            ->setDefault('route', null)
            ->setDefault('url', null)
            ->setDefault('parameters', [])
            ->setAllowedTypes('parameters', 'array');

        // menu item displayed text
        $resolver
            ->setDefault('text', '');

        // menu item html attributes
        $resolver
            ->setDefault('attr', [])
            ->setNormalizer('attr', function(Options $options, $attr) {

                if (!is_array($attr)) {
                    $attr = [];
                }

                if (empty($attr['id'])) {
                    $attr['id'] = uniqid('admin-menu-');
                }

                return $attr;
            });

        // menu sub item
        $resolver
            ->setDefault('items', [])
            ->setNormalizer('items', function(Options $options, $items) {

                if (!is_array($items)) {
                    $items = [];
                }
                $resolver = new OptionsResolver();
                $resolvedItems = [];

                foreach ($items as $name => $item) {
                    $itemConfiguration = new MenuItemConfiguration();
                    $itemConfiguration->configureOptions($resolver);
                    $itemConfiguration->setParameters($resolver->resolve($item));

                    $resolvedItems[$name] = $itemConfiguration;
                }

                return $resolvedItems;
            });

        $resolver->setDefault('icon', null);
    }
}
