<?php

declare(strict_types=1);

namespace LAG\AdminBundle\Metadata;

use LAG\AdminBundle\Bridge\Doctrine\ORM\State\ORMDataProcessor;
use LAG\AdminBundle\Bridge\Doctrine\ORM\State\ORMDataProvider;
use LAG\AdminBundle\Controller\Resource\ResourceCollectionController;

class GetCollection extends CollectionOperation
{
    public function __construct(
        ?string $name = 'index',
        ?string $title = null,
        ?string $description = null,
        ?string $icon = null,
        ?string $template = '@LAGAdmin/crud/index.html.twig',
        array $permissions = [],
        ?string $controller = ResourceCollectionController::class,
        ?string $route = null,
        array $routeParameters = [],
        array $methods = [],
        ?string $path = null,
        ?string $redirectRoute = null,
        array $redirectRouteParameters = [],
        array $properties = [],
        ?string $formType = null,
        array $formOptions = [],
        string $processor = ORMDataProcessor::class,
        string $provider = ORMDataProvider::class,
        array $identifiers = [],
        ?array $contextualActions = null,
        ?array $itemActions = null,
        bool $pagination = true,
        int $itemPerPage = 25,
        string $pageParameter = 'page',
        array $criteria = [],
        array $orderBy = [],
        ?array $filters = null,
        ?string $grid = 'table',
        ?string $targetResource = null,
        ?string $redirectOperation = null,
    ) {
        parent::__construct(
            $name,
            $title,
            $description,
            $icon,
            $template,
            $permissions,
            $controller,
            $route,
            $routeParameters,
            $methods,
            $path,
            $redirectRoute,
            $redirectRouteParameters,
            $properties,
            $formType,
            $formOptions,
            $processor,
            $provider,
            $identifiers,
            $contextualActions,
            $itemActions,
            $pagination,
            $itemPerPage,
            $pageParameter,
            $criteria,
            $orderBy,
            $filters,
            $grid,
            $targetResource,
            $redirectOperation,
        );
    }
}
