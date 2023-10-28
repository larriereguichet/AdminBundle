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
            ->define('name')
            ->default('lag_admin')
            ->allowedTypes('string')    

            ->define('title')
            ->default('Admin Bundle')
            ->allowedTypes('string')


            ->define('resource_paths')
            ->allowedTypes('array')

            ->define('translation_domain')
            ->default('admin')
            ->allowedTypes('string')

            ->define('description')
            ->default('Admin Bundle')
            ->allowedTypes('string')

            ->define('date_format')
            ->default('medium')
            ->allowedTypes('string')

            ->define('time_format')
            ->default('short')
            ->allowedTypes('string')

            ->define('date_localization')
            ->default(true)
            ->allowedTypes('boolean')

            ->define('resource_events')
            ->default(true)
            ->allowedTypes('boolean')

            ->define('filter_events')
            ->default(true)
            ->allowedTypes('boolean')

            ->define('grids')
            ->default([])
        ;
    }
}
