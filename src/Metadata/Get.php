<?php

declare(strict_types=1);

namespace LAG\AdminBundle\Metadata;

use LAG\AdminBundle\Bridge\Doctrine\ORM\State\ORMDataProcessor;
use LAG\AdminBundle\Bridge\Doctrine\ORM\State\ORMDataProvider;
use LAG\AdminBundle\Controller\Resource\ResourceController;

/**
 * The get operation is used to show a resource in a read-only view. Usually the processor is not used.
 */
class Get extends Operation
{
    public function __construct(
        ?string $name = 'show',
        string $title = null,
        string $description = null,
        string $icon = null,
        ?string $template = '@LAGAdmin/crud/show.html.twig',
        ?array $permissions = [],
        ?string $controller = ResourceController::class,
        string $route = null,
        array $routeParameters = null,
        array $methods = ['GET'],
        string $path = null,
        string $redirectRoute = null,
        array $redirectRouteParameters = null,
        array $properties = [],
        string $formType = null,
        array $formOptions = [],
        string $processor = ORMDataProcessor::class,
        string $provider = ORMDataProvider::class,
        array $identifiers = ['id'],
        array $contextualActions = null,
        array $itemActions = null,
        string $redirectResource = null,
        string $redirectOperation = null,
    ) {
        parent::__construct(
            name: $name,
            title: $title,
            description: $description,
            icon: $icon,
            template: $template,
            permissions: $permissions,
            controller: $controller,
            route: $route,
            routeParameters: $routeParameters,
            methods: $methods,
            path: $path,
            redirectRoute: $redirectRoute,
            redirectRouteParameters: $redirectRouteParameters,
            properties: $properties,
            formType: $formType,
            formOptions: $formOptions,
            processor: $processor,
            provider: $provider,
            identifiers: $identifiers,
            contextualActions: $contextualActions,
            itemActions: $itemActions,
            redirectResource: $redirectResource,
            redirectOperation: $redirectOperation,
        );
    }
}
