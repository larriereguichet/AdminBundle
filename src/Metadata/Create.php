<?php

declare(strict_types=1);

namespace LAG\AdminBundle\Metadata;

use LAG\AdminBundle\Bridge\Doctrine\ORM\State\Processor\ORMProcessor;
use LAG\AdminBundle\Bridge\Doctrine\ORM\State\Provider\ORMProvider;
use LAG\AdminBundle\Controller\Resource\ProcessResource;

/**
 * The create operation use a form to create a new resource. The provider should retrieve the resource and the processor
 * should persist it.
 */
class Create extends Operation
{
    public function __construct(
        string $shortName = 'create',
        array $context = [],
        ?string $title = null,
        ?string $description = null,
        ?string $icon = null,
        ?string $template = '@LAGAdmin/resources/create.html.twig',
        ?string $baseTemplate = null,
        ?array $permissions = null,
        ?string $controller = ProcessResource::class,
        ?string $route = null,
        ?array $routeParameters = null,
        array $methods = ['POST', 'GET'],
        ?string $path = null,
        ?string $redirectRoute = null,
        ?array $redirectRouteParameters = null,
        ?string $form = null,
        ?array $formOptions = null,
        ?string $formTemplate = null,
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
        ?string $workflow = null,
        ?string $workflowTransition = null,
        bool $partial = false,
        string|false|null $successMessage = null,
    ) {
        parent::__construct(
            shortName: $shortName,
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
            form: $form,
            formOptions: $formOptions,
            formTemplate: $formTemplate,
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
            workflow: $workflow,
            workflowTransition: $workflowTransition,
            partial: $partial,
            successMessage: $successMessage,
        );
    }
}
