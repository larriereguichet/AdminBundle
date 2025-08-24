<?php

declare(strict_types=1);

namespace LAG\AdminBundle\Metadata;

use LAG\AdminBundle\Bridge\Doctrine\ORM\State\Processor\ORMProcessor;
use LAG\AdminBundle\Bridge\Doctrine\ORM\State\Provider\ORMProvider;
use LAG\AdminBundle\Controller\Resource\ShowResource;

/**
 * The show operation is used to show a resource in a read-only view. Usually the processor is not used.
 */
class Show extends Operation
{
    public function __construct(
        string $name = 'show',
        array $context = [],
        ?string $title = null,
        ?string $description = null,
        ?string $icon = null,
        ?string $template = '@LAGAdmin/resources/show.html.twig',
        ?string $baseTemplate = null,
        ?array $permissions = null,
        ?string $controller = ShowResource::class,
        ?string $route = null,
        ?array $routeParameters = null,
        array $methods = ['GET'],
        ?string $path = null,
        ?string $redirectRoute = null,
        ?array $redirectRouteParameters = null,
        string $processor = ORMProcessor::class,
        string $provider = ORMProvider::class,
        ?array $identifiers = null,
        ?array $contextualActions = null,
        ?array $itemActions = null,
        ?string $redirectOperation = null,
        ?bool $validation = true,
        ?array $validationContext = null,
        ?bool $ajax = true,
        ?array $normalizationContext = null,
        ?array $denormalizationContext = null,
        ?string $input = null,
        ?string $output = null,
        bool $partial = false,
        ?string $successMessage = null,
    ) {
        parent::__construct(
            name: $name,
            context: $context,
            title: $title,
            description: $description,
            icon: $icon,
            template: $template,
            baseTemplate: $baseTemplate,
            permissions: $permissions,
            controller: $controller,
            route: $route,
            routeParameters: $routeParameters,
            methods: $methods,
            path: $path,
            redirectRoute: $redirectRoute,
            redirectRouteParameters: $redirectRouteParameters,
            processor: $processor,
            provider: $provider,
            identifiers: $identifiers,
            contextualActions: $contextualActions,
            itemActions: $itemActions,
            redirectOperation: $redirectOperation,
            validation: $validation,
            validationContext: $validationContext,
            ajax: $ajax,
            normalizationContext: $normalizationContext,
            denormalizationContext: $denormalizationContext,
            input: $input,
            output: $output,
            partial: $partial,
            successMessage: $successMessage,
        );
    }
}
