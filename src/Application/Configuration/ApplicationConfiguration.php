<?php

declare(strict_types=1);

namespace LAG\AdminBundle\Application\Configuration;

use JK\Configuration\ServiceConfiguration;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Application configuration class. Allow easy configuration manipulation within an Admin.
 */
class ApplicationConfiguration extends ServiceConfiguration
{
    protected function configureOptions(OptionsResolver $resolver): void
    {
        $resolver
            ->setRequired('resource_paths')
            ->setAllowedTypes('resource_paths', 'array')

            ->setDefault('translation_domain', 'admin')
            ->setAllowedTypes('translation_domain', 'string')

            ->setDefault('title', 'Admin Bundle')
            ->setAllowedTypes('title', 'string')

            ->setDefault('description', 'Admin Bundle')
            ->setAllowedTypes('description', 'string')

            ->setDefault('date_format', 'medium')
            ->setAllowedTypes('date_format', 'string')

            ->setDefault('time_format', 'short')
            ->setAllowedTypes('time_format', 'string')

            ->setDefault('date_localization', true)
            ->setAllowedTypes('date_localization', 'boolean')
        ;
    }
}
