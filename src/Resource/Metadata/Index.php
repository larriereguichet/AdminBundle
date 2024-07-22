<?php

declare(strict_types=1);

namespace LAG\AdminBundle\Resource\Metadata;

use LAG\AdminBundle\Bridge\Doctrine\ORM\State\Processor\ORMProcessor;
use LAG\AdminBundle\Bridge\Doctrine\ORM\State\Provider\ORMProvider;
use LAG\AdminBundle\Controller\Resource\ResourceCollectionController;
use LAG\AdminBundle\Form\Type\Resource\FilterType;

/**
 * The get collection operation is used to show a collection of resources, usually in a grid. The provider should return
 * a collection or a pager. The processor could handle the form filtering processing for instance.
 */
class Index extends CollectionOperation
{
    public function __construct(
        string $name = 'index',
        string $title = null,
        string $description = null,
        string $icon = null,
        ?string $template = '@LAGAdmin/resources/index.html.twig',
        array $permissions = [],
        ?string $controller = ResourceCollectionController::class,
        string $route = null,
        array $routeParameters = [],
        array $methods = [],
        string $path = null,
        string $redirectRoute = null,
        array $redirectRouteParameters = [],
        string $form = null,
        array $formOptions = [],
        string $processor = ORMProcessor::class,
        string $provider = ORMProvider::class,
        ?array $identifiers = null,
        array $contextualActions = null,
        array $itemActions = null,
        string $redirectApplication = null,
        string $redirectResource = null,
        string $redirectOperation = null,
        ?bool $validation = true,
        array $validationContext = null,
        ?bool $ajax = true,
        array $normalizationContext = null,
        array $denormalizationContext = null,
        ?string $input = null,
        ?string $output = null,
        ?string $workflow = null,
        ?string $workflowTransition = null,
        bool $pagination = true,
        int $itemsPerPage = 25,
        string $pageParameter = 'page',
        array $criteria = [],
        array $orderBy = [],
        array $filters = [],
        ?string $grid = null,
        array $gridOptions = [],
        ?string $filterFormType = FilterType::class,
        array $filterFormOptions = [],
        ?string $itemForm = null,
        ?array $itemFormOptions = null,
        ?string $collectionForm = null,
        ?array $collectionFormOptions = null,
        bool $partial = false,
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
            form: $form,
            formOptions: $formOptions,
            processor: $processor,
            provider: $provider,
            identifiers: $identifiers,
            contextualActions: $contextualActions,
            itemActions: $itemActions,
            redirectApplication: $redirectApplication,
            redirectResource: $redirectResource,
            redirectOperation: $redirectOperation,
            normalizationContext: $normalizationContext,
            denormalizationContext: $denormalizationContext,
            input: $input,
            output: $output,
            validation: $validation,
            validationContext: $validationContext,
            ajax: $ajax,
            workflow: $workflow,
            workflowTransition: $workflowTransition,
            pagination: $pagination,
            itemsPerPage: $itemsPerPage,
            pageParameter: $pageParameter,
            criteria: $criteria,
            orderBy: $orderBy,
            filters: $filters,
            grid: $grid,
            gridOptions: $gridOptions,
            filterFormType: $filterFormType,
            filterFormOptions: $filterFormOptions,
            itemForm: $itemForm,
            itemFormOptions: $itemFormOptions,
            collectionForm: $collectionForm,
            collectionFormOptions: $collectionFormOptions,
            partial: $partial,
        );
    }
}
