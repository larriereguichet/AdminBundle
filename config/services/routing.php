<?php

declare(strict_types=1);

namespace Symfony\Component\DependencyInjection\Loader\Configurator;

use LAG\AdminBundle\Metadata\Registry\ResourceRegistryInterface;
use LAG\AdminBundle\Routing\Loader\ResourceRoutingLoader;
use LAG\AdminBundle\Routing\Route\RouteNameGeneratorInterface;
use LAG\AdminBundle\Routing\UrlGenerator\PathGenerator;
use LAG\AdminBundle\Routing\UrlGenerator\PathGeneratorInterface;
use LAG\AdminBundle\Routing\UrlGenerator\UrlGenerator;
use LAG\AdminBundle\Routing\UrlGenerator\UrlGeneratorInterface;

return static function (ContainerConfigurator $container): void {
    $services = $container->services();

    $services->set(ResourceRoutingLoader::class)
        ->arg('$pathGenerator', service(PathGeneratorInterface::class))
        ->arg('$resourceRegistry', service(ResourceRegistryInterface::class))
        ->tag('routing.loader')
    ;

    $services->set(RouteNameGeneratorInterface::class);
    $services->set(UrlGeneratorInterface::class, UrlGenerator::class)
        ->arg('$router', service('router'))
        ->arg('$resourceRegistry', service(ResourceRegistryInterface::class))
    ;

    $services->set(PathGeneratorInterface::class, PathGenerator::class);
};
