<?php

namespace LAG\AdminBundle\Metadata;

use LAG\AdminBundle\Bridge\Doctrine\ORM\DataProcessor\ORMDataProcessor;
use LAG\AdminBundle\Bridge\Doctrine\ORM\DataProvider\ORMDataProvider;

class Index extends Action
{
    public function __construct(
        ?string $name = 'index',
        ?string $title = null,
        ?string $description = null,
        ?string $icon = 'fa-list',
        ?string $template = '@LAGAdmin/crud/index.html.twig',
        ?array $permissions = ['ROLE_ADMIN'],
        ?string $controller = \LAG\AdminBundle\Controller\Index::class,
        ?string $route = null,
        ?array $routeParameters = null,
        ?string $path = null,
        ?string $targetRoute = null,
        ?array $targetRouteParameters = null,
        ?array $fields = null,
        ?string $formType = null,
        array $formTypeOptions = [],
        array $collectionActions = [],
        array $itemActions = [],
        string $processor = ORMDataProcessor::class,
        string $provider = ORMDataProvider::class,
        public readonly int $itemPerPage = 25,
        public readonly ?array $order = null,
        public readonly ?array $filters = null,
        public readonly bool $pagination = true,
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
            $path,
            $targetRoute,
            $targetRouteParameters,
            $fields,
            $formType,
            $formTypeOptions,
            $collectionActions,
            $itemActions,
            $processor,
            $provider,
        );
    }
}
