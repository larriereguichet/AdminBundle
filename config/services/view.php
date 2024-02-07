<?php

declare(strict_types=1);

namespace Symfony\Component\DependencyInjection\Loader\Configurator;

use LAG\AdminBundle\Grid\Render\CellRendererInterface;
use LAG\AdminBundle\Grid\Render\GridRendererInterface;
use LAG\AdminBundle\Grid\Render\LinkRenderer;
use LAG\AdminBundle\Grid\Render\LinkRendererInterface;
use LAG\AdminBundle\Resource\Context\ResourceContextInterface;
use LAG\AdminBundle\Resource\Registry\ResourceRegistryInterface;
use LAG\AdminBundle\Routing\UrlGenerator\UrlGeneratorInterface;
use LAG\AdminBundle\Twig\Extension\AdminExtension;
use LAG\AdminBundle\Twig\Extension\GridExtension;
use LAG\AdminBundle\Twig\Extension\RoutingExtension;
use LAG\AdminBundle\Twig\Extension\StringExtension;
use LAG\AdminBundle\View\Component\Cell\Cell;
use LAG\AdminBundle\View\Component\Cell\Text;
use LAG\AdminBundle\View\Component\Grid\CardGrid;
use LAG\AdminBundle\View\Component\Grid\TableGrid;
use LAG\AdminBundle\View\Component\Grid\TableHeader;
use LAG\AdminBundle\View\Helper\RoutingHelper;

return static function (ContainerConfigurator $container): void {
    $services = $container->services();

    // Extensions
    $services->set(AdminExtension::class)
        ->arg('$security', service('security.helper'))
        ->arg('$linkRenderer', service(LinkRendererInterface::class))
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
        ->arg('$translationDomain', param('lag_admin.translation_domain'))
        ->arg('$translator', service('translator'))
        ->tag('twig.extension')
    ;
    $services->set(LinkRendererInterface::class, LinkRenderer::class)
        ->arg('$urlGenerator', service(UrlGeneratorInterface::class))
        ->arg('$validator', service('validator'))
        ->arg('$environment', service('twig'))
    ;
    $services->set(RoutingExtension::class)
        ->arg('$helper', service(RoutingHelper::class))
        ->tag('twig.extension')
    ;

    // Components
//    $services->set(FormComponent::class)
//        ->arg('$formFactory', service('form.factory'))
//        ->tag('twig.component')
//    ;
    $services->set(CardGrid::class)
        ->tag('twig.component', [
            'key' => 'lag_admin:grid:card',
            'template' => '@LAGAdmin/components/grids/card.html.twig',
        ])
    ;
    $services->set(TableGrid::class)->autoconfigure();
    $services->set(TableHeader::class)->autoconfigure();
    $services->set(Cell::class)->autoconfigure();
    $services->set(Text::class)->autoconfigure();

    // Helpers
    $services->set(RoutingHelper::class)
        ->arg('$context', service(ResourceContextInterface::class))
        ->arg('$requestStack', service('request_stack'))
        ->arg('$urlGenerator', service(UrlGeneratorInterface::class))
    ;
};
