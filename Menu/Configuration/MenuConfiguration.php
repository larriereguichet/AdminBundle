<?php

namespace LAG\AdminBundle\Menu\Configuration;

use LAG\AdminBundle\Configuration\Configuration;
use Symfony\Component\OptionsResolver\Options;
use Symfony\Component\OptionsResolver\OptionsResolver;

class MenuConfiguration extends Configuration
{
    /**
     * Define allowed parameters and values for this configuration, using optionsResolver component.
     *
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        // menu html attributes
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
            })
        ;

        // menu item
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
            })
        ;
    }
}
