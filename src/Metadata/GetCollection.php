<?php

declare(strict_types=1);

namespace LAG\AdminBundle\Metadata;

use LAG\AdminBundle\Bridge\Doctrine\ORM\State\ORMDataProcessor;
use LAG\AdminBundle\Bridge\Doctrine\ORM\State\ORMDataProvider;
use LAG\AdminBundle\Controller\Resource\ResourceCollectionController;
use LAG\AdminBundle\Form\Type\Resource\FilterType;

/**
 * The get collection operation is used to show a collection of resources, usually in a grid. The provider should return
 * a collection or a pager. The processor could handle the form filtering processing for instance.
 */
class GetCollection extends CollectionOperation
{
    public function __construct(
        string $name = 'get_collection',
        string $title = null,
        string $description = null,
        string $icon = null,
        ?string $template = '@LAGAdmin/crud/index.html.twig',
        array $permissions = [],
        ?string $controller = ResourceCollectionController::class,
        string $route = null,
        array $routeParameters = [],
        array $methods = [],
        string $path = null,
        string $redirectRoute = null,
        array $redirectRouteParameters = [],
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
        ?bool $validation = true,
        array $validationContext = null,
        ?bool $ajax = true,
        array $normalizationContext = null,
        array $denormalizationContext = null,
        bool $pagination = true,
        int $itemPerPage = 25,
        string $pageParameter = 'page',
        array $criteria = [],
        array $orderBy = [],
        array $filters = null,
        ?string $grid = 'table',
        ?string $filterFormType = FilterType::class,
        array $filterFormOptions = [],
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
            pagination: $pagination,
            itemPerPage: $itemPerPage,
            pageParameter: $pageParameter,
            criteria: $criteria,
            orderBy: $orderBy,
            filters: $filters,
            grid: $grid,
            redirectResource: $redirectResource,
            redirectOperation: $redirectOperation,
            filterFormType: $filterFormType,
            filterFormOptions: $filterFormOptions,
            validation: $validation,
            validationContext: $validationContext,
            ajax: $ajax,
            normalizationContext: $normalizationContext,
            denormalizationContext: $denormalizationContext,
        );
    }
}
