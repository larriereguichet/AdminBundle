<?php

declare(strict_types=1);

namespace Symfony\Component\DependencyInjection\Loader\Configurator;

use LAG\AdminBundle\Application\Configuration\ApplicationConfiguration;
use LAG\AdminBundle\Grid\View\CellRendererInterface;
use LAG\AdminBundle\Grid\View\GridRendererInterface;
use LAG\AdminBundle\Grid\View\LinkRenderer;
use LAG\AdminBundle\Grid\View\LinkRendererInterface;
use LAG\AdminBundle\Metadata\Registry\ResourceRegistryInterface;
use LAG\AdminBundle\Routing\UrlGenerator\UrlGeneratorInterface;
use LAG\AdminBundle\Twig\Extension\AdminExtension;
use LAG\AdminBundle\Twig\Extension\GridExtension;
use LAG\AdminBundle\Twig\Extension\StringExtension;

return static function (ContainerConfigurator $container): void {
    $services = $container->services();

    $services->set(AdminExtension::class)
        ->arg('$applicationConfiguration', service(ApplicationConfiguration::class))
        ->arg('$security', service('security.helper'))
        ->arg('$actionRenderer', service(LinkRendererInterface::class))
        ->arg('$urlGenerator', service(UrlGeneratorInterface::class))
        ->arg('$registry', service(ResourceRegistryInterface::class))
        ->tag('twig.extension')
    ;

    $services->set(GridExtension::class)
        ->arg('$gridRenderer', service(GridRendererInterface::class))
        ->arg('$cellRenderer', service(CellRendererInterface::class))
        ->tag('twig.extension')
    ;

    $services->set(StringExtension::class)
        ->arg('$configuration', service(ApplicationConfiguration::class))
        ->arg('$translator', service('translator'))
        ->tag('twig.extension')
    ;

    $services->set(LinkRendererInterface::class, LinkRenderer::class)
        ->arg('$urlGenerator', service(UrlGeneratorInterface::class))
        ->arg('$validator', service('validator'))
        ->arg('$environment', service('twig'))
    ;
};
