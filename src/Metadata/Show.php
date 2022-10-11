<?php

namespace LAG\AdminBundle\Metadata;

use LAG\AdminBundle\Bridge\Doctrine\ORM\State\ORMDataProcessor;
use LAG\AdminBundle\Bridge\Doctrine\ORM\State\ORMDataProvider;

class Show extends Operation
{
    public function __construct(
        ?string $name = 'show',
        ?string $resourceName = null,
        ?string $title = null,
        ?string $description = null,
        ?string $icon = null,
        ?string $template = '@LAGAdmin/crud/show.html.twig',
        ?array $permissions = [],
        ?string $controller = \LAG\AdminBundle\Controller\Show::class,
        ?string $route = null,
        ?array $routeParameters = null,
        array $methods = ['POST', 'GET'],
        string $path = '/show',
        ?string $targetRoute = null,
        ?array $targetRouteParameters = null,
        array $properties = [],
        ?string $formType = null,
        array $formOptions = [],
        string $processor = ORMDataProcessor::class,
        string $provider = ORMDataProvider::class,
        array $identifiers = [],
        ?array $itemActions = null
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
        );
    }
}
