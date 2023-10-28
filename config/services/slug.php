<?php

declare(strict_types=1);

namespace Symfony\Component\DependencyInjection\Loader\Configurator;

use LAG\AdminBundle\Slug\Generator\CompositeSlugGenerator;
use LAG\AdminBundle\Slug\Generator\SimpleSlugGenerator;
use LAG\AdminBundle\Slug\Generator\SlugGeneratorInterface;
use LAG\AdminBundle\Slug\Mapping\SlugMapping;
use LAG\AdminBundle\Slug\Mapping\SlugMappingInterface;

return static function (ContainerConfigurator $container): void {
    $services = $container->services();

    $services->set(SlugGeneratorInterface::class, CompositeSlugGenerator::class);
    $services->set(SimpleSlugGenerator::class)
        ->tag('lag_admin.slug_generator', [
            'generator' => 'default',
        ])
    ;

    $services->set(SlugMappingInterface::class, SlugMapping::class)
        ->arg('$registry', service('lag_admin.resource.registry'))
    ;
};
