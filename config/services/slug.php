<?php

declare(strict_types=1);

namespace Symfony\Component\DependencyInjection\Loader\Configurator;

use LAG\AdminBundle\Slug\Registry\SluggerRegistry;
use LAG\AdminBundle\Slug\Registry\SluggerRegistryInterface;
use LAG\AdminBundle\Slug\Slugger\DefaultSlugger;
use LAG\AdminBundle\Slug\Slugger\DefaultSluggerInterface;

return static function (ContainerConfigurator $container): void {
    $services = $container->services();

    // Registry
    $services->set(SluggerRegistryInterface::class, SluggerRegistry::class)
        ->arg('$sluggers', tagged_iterator(tag: 'lag_admin.slugger', indexAttribute: 'name'))
    ;

    // Slugger
    $services->set(DefaultSluggerInterface::class, DefaultSlugger::class)
        ->tag('lag_admin.slugger', ['name' => 'default'])
    ;
};
