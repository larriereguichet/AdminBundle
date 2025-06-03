<?php

declare(strict_types=1);

namespace Symfony\Component\DependencyInjection\Loader\Configurator;

use LAG\AdminBundle\Bridge\QuillJs\Render\QuillJsRendererInterface;
use LAG\AdminBundle\Routing\UrlGenerator\ResourceUrlGeneratorInterface;
use LAG\AdminBundle\Twig\Extension\PaginationExtension;
use LAG\AdminBundle\Twig\Extension\RenderExtension;
use LAG\AdminBundle\Twig\Extension\RoutingExtension;
use LAG\AdminBundle\Twig\Extension\SecurityExtension;
use LAG\AdminBundle\Twig\Extension\TextExtension;
use LAG\AdminBundle\View\Component\Cell\Actions;
use LAG\AdminBundle\View\Component\Cell\FormComponent;
use LAG\AdminBundle\View\Component\Cell\ImageComponent;
use LAG\AdminBundle\View\Component\Cell\Link;
use LAG\AdminBundle\View\Component\Cell\MapComponent;
use LAG\AdminBundle\View\Component\Cell\TextComponent;
use LAG\AdminBundle\View\Component\Grid;
use LAG\AdminBundle\View\Component\Grid\GridCell;
use LAG\AdminBundle\View\Component\Grid\GridHeader;
use LAG\AdminBundle\View\Helper\PaginationHelper;
use LAG\AdminBundle\View\Helper\RenderHelper;
use LAG\AdminBundle\View\Helper\RoutingHelper;
use LAG\AdminBundle\View\Helper\SecurityHelper;
use LAG\AdminBundle\View\Helper\TextHelper;
use LAG\AdminBundle\View\Render\ActionRenderer;
use LAG\AdminBundle\View\Render\ActionRendererInterface;
use LAG\AdminBundle\View\Render\LinkRenderer;
use LAG\AdminBundle\View\Render\LinkRendererInterface;

return static function (ContainerConfigurator $container): void {
    $services = $container->services();

    // Extensions
    $services->set(RenderExtension::class)->tag('twig.extension');
    $services->set(PaginationExtension::class)->tag('twig.extension');
    $services->set(RoutingExtension::class)->tag('twig.extension');
    $services->set(RenderExtension::class)->tag('twig.extension');
    $services->set(SecurityExtension::class)->tag('twig.extension');
    $services->set(TextExtension::class)->tag('twig.extension');

    // Runtime extensions
    $services->set(RoutingHelper::class)
        ->args(['$urlGenerator' => service(ResourceUrlGeneratorInterface::class)])
        ->tag('twig.runtime')
    ;
    $services->set(RenderHelper::class)
        ->arg('$linkRenderer', service(LinkRendererInterface::class))
        ->arg('$actionRenderer', service(ActionRendererInterface::class))
        ->tag('twig.runtime')
    ;
    $services->set(PaginationHelper::class)->tag('twig.runtime');
    $services->set(SecurityHelper::class)
        ->arg('$operationFactory', service('lag_admin.operation.factory'))
        ->arg('$security', service('security.helper'))
        ->tag('twig.runtime')
    ;
    $services->set(TextHelper::class)
        ->arg('$quillJsRenderer', service(QuillJsRendererInterface::class))
        ->tag('twig.runtime')
    ;

    // Components
    $services->set(Grid::class)->autoconfigure();
    $services->set(GridHeader::class)->autoconfigure();
    $services->set(GridCell::class)->autoconfigure();

    $services->set(TextComponent::class)->autoconfigure();
    $services->set(Link::class)->autoconfigure();
    $services->set(Actions::class)->autoconfigure();
    $services->set(MapComponent::class)->autoconfigure();
    $services->set(ImageComponent::class)->autoconfigure();
    $services->set(FormComponent::class)
        ->autoconfigure()
        ->arg('$formFactory', service('form.factory'))
    ;

    // Renderer
    $services->set(LinkRendererInterface::class, LinkRenderer::class)
        ->arg('$urlGenerator', service(ResourceUrlGeneratorInterface::class))
        ->arg('$validator', service('validator'))
        ->arg('$environment', service('twig'))
    ;
    $services->set(ActionRendererInterface::class, ActionRenderer::class)
        ->args([
            '$urlGenerator' => service(ResourceUrlGeneratorInterface::class),
            '$environment' => service('twig'),
            '$translator' => service('translator')
        ])
    ;
};
