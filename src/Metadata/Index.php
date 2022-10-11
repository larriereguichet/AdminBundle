<?php

namespace LAG\AdminBundle\Metadata;

use LAG\AdminBundle\Bridge\Doctrine\ORM\State\ORMDataProcessor;
use LAG\AdminBundle\Bridge\Doctrine\ORM\State\ORMDataProvider;

class Index extends CollectionOperation
{
    public function __construct(
        ?string $name = 'index',
        ?string $resourceName = null,
        ?string $title = null,
        ?string $description = null,
        ?string $icon = null,
        ?string $template = '@LAGAdmin/crud/index.html.twig',
        array $permissions = [],
        ?string $controller = \LAG\AdminBundle\Controller\Index::class,
        ?string $route = null,
        array $routeParameters = [],
        array $methods = [],
        string $path = '',
        ?string $targetRoute = null,
        array $targetRouteParameters = [],
        array $properties = [],
        ?string $formType = null,
        array $formOptions = [],
        string $processor = ORMDataProcessor::class,
        string $provider = ORMDataProvider::class,
        array $identifiers = ['id'],
        ?array $itemActions = null,
        bool $pagination = true,
        int $itemPerPage = 25,
        string $pageParameter = 'page',
        array $criteria = [],
        array $orderBy = [],
        ?array $filters = null,
        ?array $listActions = null,
        ?string $gridTemplate = '@LAGAdmin/grid/table_grid.html.twig',
    ) {
        parent::__construct(
            $name,
            $resourceName,
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
            $targetRoute,
            $targetRouteParameters,
            $properties,
            $formType,
            $formOptions,
            $processor,
            $provider,
            $identifiers,
            $itemActions,
            $pagination,
            $itemPerPage,
            $pageParameter,
            $criteria,
            $orderBy,
            $filters,
            $listActions,
            $gridTemplate,
        );
    }
}
