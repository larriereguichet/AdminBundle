<?php

declare(strict_types=1);

namespace LAG\AdminBundle\Metadata\Attribute;

use LAG\AdminBundle\Bridge\Doctrine\ORM\State\Processor\ORMProcessor;
use LAG\AdminBundle\Bridge\Doctrine\ORM\State\Provider\ORMProvider;
use LAG\AdminBundle\Controller\Resource\IndexResources;

/**
 * The index operation is used to show a collection of resources, usually in a grid. The provider should return
 * a collection or a pager. The processor could handle the form filtering processing for instance.
 */
class Index extends CollectionOperation
{
    /**
     * @param array<string, mixed> $context
     * @param array<string>|null $permissions
     * @param array<string, mixed>|null $routeParameters
     * @param array<string> $methods
     * @param array<string, mixed> $redirectRouteParameters
     * @param array<string, mixed>|null $formOptions
     * @param array<string>|null $identifiers
     * @param array<string, mixed>|null $contextualLinks
     * @param array<string, mixed>|null $itemLinks
     * @param array<string, mixed>|null $validationContext
     * @param array<string, mixed>|null $normalizationContext
     * @param array<string, mixed>|null $denormalizationContext
     * @param array<int|string, mixed> $filters
     * @param array<string, mixed> $gridOptions
     * @param array<string, mixed> $filterFormOptions
     * @param string[] $batchOperations
     * @param array<int|string, mixed>|null $collectionLinks
     */
    public function __construct(
        string $name = 'index',
        array $context = [],
        string|false|null $title = null,
        ?string $description = null,
        ?string $icon = null,
        ?string $template = '@LAGAdmin/resources/index.html.twig',
        ?string $baseTemplate = null,
        ?array $permissions = null,
        ?string $controller = IndexResources::class,
        ?string $route = null,
        ?array $routeParameters = null,
        array $methods = [],
        ?string $path = null,
        ?string $redirectRoute = null,
        array $redirectRouteParameters = [],
        ?string $form = null,
        ?array $formOptions = null,
        ?string $formTemplate = null,
        string $processor = ORMProcessor::class,
        string $provider = ORMProvider::class,
        ?array $identifiers = null,
        ?array $contextualLinks = null,
        ?array $itemLinks = null,
        ?string $redirectOperation = null,
        ?bool $validation = true,
        ?array $validationContext = null,
        ?bool $ajax = true,
        ?array $normalizationContext = null,
        ?array $denormalizationContext = null,
        ?string $input = null,
        ?string $output = null,
        ?string $normalizationInput = null,
        ?string $normalizationOutput = null,
        ?string $workflow = null,
        ?string $workflowTransition = null,
        ?int $limit = null,
        bool $pagination = true,
        int $itemsPerPage = 25,
        string $pageParameter = 'page',
        array $filters = [],
        ?string $grid = null,
        array $gridOptions = [],
        ?string $filterForm = null,
        array $filterFormOptions = [],
        array $batchOperations = [],
        ?array $collectionLinks = null,
        bool $embedded = false,
        ?string $flashMessage = null,
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
            form: $form,
            formOptions: $formOptions,
            formTemplate: $formTemplate,
            processor: $processor,
            provider: $provider,
            identifiers: $identifiers,
            contextualLinks: $contextualLinks,
            itemLinks: $itemLinks,
            redirectOperation: $redirectOperation,
            validation: $validation,
            validationContext: $validationContext,
            ajax: $ajax,
            normalizationContext: $normalizationContext,
            denormalizationContext: $denormalizationContext,
            input: $input,
            output: $output,
            normalizationInput: $normalizationInput,
            normalizationOutput: $normalizationOutput,
            workflow: $workflow,
            workflowTransition: $workflowTransition,
            embedded: $embedded,
            flashMessage: $flashMessage,
            limit: $limit,
            pagination: $pagination,
            itemsPerPage: $itemsPerPage,
            pageParameter: $pageParameter,
            filters: $filters,
            grid: $grid,
            gridOptions: $gridOptions,
            collectionLinks: $collectionLinks,
            filterForm: $filterForm,
            filterFormOptions: $filterFormOptions,
            batchOperations: $batchOperations,
        );
    }
}
