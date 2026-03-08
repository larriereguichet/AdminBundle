<?php

declare(strict_types=1);

namespace Symfony\Component\DependencyInjection\Loader\Configurator;

use LAG\AdminBundle\RichText\RichTextRendererInterface;
use LAG\AdminBundle\Routing\UrlGenerator\OperationUrlGeneratorInterface;
use LAG\AdminBundle\Twig\Extension\AttributeExtension;
use LAG\AdminBundle\Twig\Extension\PaginationExtension;
use LAG\AdminBundle\Twig\Extension\RenderExtension;
use LAG\AdminBundle\Twig\Extension\RichTextExtension;
use LAG\AdminBundle\Twig\Extension\RoutingExtension;
use LAG\AdminBundle\Twig\Extension\SecurityExtension;
use LAG\AdminBundle\Twig\Runtime\AttributeRuntime;
use LAG\AdminBundle\Twig\Runtime\PaginationHelper;
use LAG\AdminBundle\Twig\Runtime\RenderRuntime;
use LAG\AdminBundle\Twig\Runtime\RichTextRuntime;
use LAG\AdminBundle\Twig\Runtime\RoutingRuntime;
use LAG\AdminBundle\Twig\Runtime\SecurityRuntime;
use LAG\AdminBundle\View\Component\Cell\FormComponent;
use LAG\AdminBundle\View\Component\Cell\ImageComponent;
use LAG\AdminBundle\View\Component\Cell\Link;
use LAG\AdminBundle\View\Component\Cell\MapComponent;
use LAG\AdminBundle\View\Component\Text;
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
    $services->set(RichTextExtension::class)->tag('twig.extension');
    $services->set(AttributeExtension::class)->tag('twig.extension');

    // Runtime extensions
    $services->set(RoutingRuntime::class)
        ->args(['$urlGenerator' => service(OperationUrlGeneratorInterface::class)])
        ->tag('twig.runtime')
    ;
    $services->set(RenderRuntime::class)
        ->arg('$linkRenderer', service(LinkRendererInterface::class))
        ->tag('twig.runtime')
    ;
    $services->set(PaginationHelper::class)->tag('twig.runtime');
    $services->set(SecurityRuntime::class)
        ->arg('$operationFactory', service('lag_admin.operation.factory'))
        ->arg('$security', service('security.helper'))
        ->tag('twig.runtime')
    ;
    $services->set(RichTextRuntime::class)
        ->arg('$quillJsRenderer', service(RichTextRendererInterface::class))
        ->tag('twig.runtime')
    ;
    $services->set(AttributeRuntime::class)
        ->args(['$environment' => service('twig')])
        ->tag('twig.runtime')
    ;
    $services->set(Text::class)->autoconfigure();
    $services->set(Link::class)->autoconfigure();
    $services->set(MapComponent::class)->autoconfigure();
    $services->set(ImageComponent::class)->autoconfigure();
    $services->set(FormComponent::class)
        ->autoconfigure()
        ->arg('$formFactory', service('form.factory'))
    ;

    // Renderer
    $services->set(LinkRendererInterface::class, LinkRenderer::class)
        ->arg('$urlGenerator', service(OperationUrlGeneratorInterface::class))
        ->arg('$validator', service('validator'))
        ->arg('$environment', service('twig'))
    ;
};
