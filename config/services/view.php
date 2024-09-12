<?php

declare(strict_types=1);

namespace Symfony\Component\DependencyInjection\Loader\Configurator;

use LAG\AdminBundle\Bridge\QuillJs\Render\QuillJsRendererInterface;
use LAG\AdminBundle\Grid\Render\CellRendererInterface;
use LAG\AdminBundle\Grid\Render\GridRendererInterface;
use LAG\AdminBundle\Grid\Render\HeaderRendererInterface;
use LAG\AdminBundle\Grid\Render\LinkRendererInterface;
use LAG\AdminBundle\Resource\Context\ResourceContextInterface;
use LAG\AdminBundle\Resource\Registry\ApplicationRegistryInterface;
use LAG\AdminBundle\Resource\Registry\ResourceRegistryInterface;
use LAG\AdminBundle\Routing\UrlGenerator\UrlGeneratorInterface;
use LAG\AdminBundle\Twig\Extension\AdminExtension;
use LAG\AdminBundle\Twig\Extension\GridExtension;
use LAG\AdminBundle\Twig\Extension\PaginationExtension;
use LAG\AdminBundle\Twig\Extension\RenderExtension;
use LAG\AdminBundle\Twig\Extension\RoutingExtension;
use LAG\AdminBundle\Twig\Extension\TextExtension;
use LAG\AdminBundle\View\Component\Cell\Actions;
use LAG\AdminBundle\View\Component\Cell\FormComponent;
use LAG\AdminBundle\View\Component\Cell\ImageComponent;
use LAG\AdminBundle\View\Component\Cell\Link;
use LAG\AdminBundle\View\Component\Cell\MapComponent;
use LAG\AdminBundle\View\Component\Cell\TextComponent;
use LAG\AdminBundle\View\Component\Grid\Grid;
use LAG\AdminBundle\View\Helper\GridHelper;
use LAG\AdminBundle\View\Helper\GridHelperInterface;
use LAG\AdminBundle\View\Helper\PaginationHelper;
use LAG\AdminBundle\View\Helper\PaginationHelperInterface;
use LAG\AdminBundle\View\Helper\RenderHelper;
use LAG\AdminBundle\View\Helper\RenderHelperInterface;
use LAG\AdminBundle\View\Helper\RoutingHelper;
use LAG\AdminBundle\View\Helper\RoutingHelperInterface;
use LAG\AdminBundle\View\Helper\TextHelper;
use LAG\AdminBundle\View\Helper\TextHelperInterface;
use LAG\AdminBundle\View\Vars\LAGAdminVars;
use LAG\AdminBundle\View\Vars\LAGAdminVarsInterface;

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
        ->arg('$helper', service(GridHelperInterface::class))
        ->tag('twig.extension')
    ;
    $services->set(TextExtension::class)
        ->arg('$helper', service(TextHelperInterface::class))
        ->tag('twig.extension')
    ;
    $services->set(RoutingExtension::class)
        ->arg('$helper', service(RoutingHelperInterface::class))
        ->tag('twig.extension')
    ;
    $services->set(RenderExtension::class)
        ->arg('$helper', service(RenderHelperInterface::class))
        ->tag('twig.extension')
    ;
    $services->set(PaginationExtension::class)
        ->arg('$helper', service(PaginationHelperInterface::class))
        ->tag('twig.extension')
    ;

    // Components
    $services->set(Grid::class)->autoconfigure();
    $services->set(TextComponent::class)->autoconfigure();
    $services->set(Link::class)->autoconfigure();
    $services->set(Actions::class)->autoconfigure();
    $services->set(MapComponent::class)->autoconfigure();
    $services->set(ImageComponent::class)->autoconfigure();
    $services->set(FormComponent::class)
        ->autoconfigure()
        ->arg('$formFactory', service('form.factory'))
    ;

    // Helpers
    $services->set(RoutingHelperInterface::class, RoutingHelper::class)
        ->arg('$context', service(ResourceContextInterface::class))
        ->arg('$requestStack', service('request_stack'))
        ->arg('$urlGenerator', service(UrlGeneratorInterface::class))
    ;
    $services->set(RenderHelperInterface::class, RenderHelper::class)
        ->arg('$environment', service('twig'))
        ->arg('$urlGenerator', service(UrlGeneratorInterface::class))
        ->arg('$translator', service('translator'))
    ;
    $services->set(PaginationHelperInterface::class, PaginationHelper::class);
    $services->set(GridHelperInterface::class, GridHelper::class)
        ->arg('$gridRenderer', service(GridRendererInterface::class))
        ->arg('$headerRenderer', service(HeaderRendererInterface::class))
        ->arg('$cellRenderer', service(CellRendererInterface::class))
    ;
    $services->set(TextHelperInterface::class, TextHelper::class)
        ->arg('$quillJsRenderer', service(QuillJsRendererInterface::class))
    ;

    // Factories
    $services->set(LAGAdminVarsInterface::class, LAGAdminVars::class)
        ->arg('$context', service(ResourceContextInterface::class))
        ->arg('$requestStack', service('request_stack'))
        ->arg('$applicationRegistry', service(ApplicationRegistryInterface::class))

        ->alias('lag_admin.twig_vars', LAGAdminVarsInterface::class)
    ;
};
