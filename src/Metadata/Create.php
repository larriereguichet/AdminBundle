<?php

declare(strict_types=1);

namespace LAG\AdminBundle\Metadata;

use LAG\AdminBundle\Bridge\Doctrine\ORM\State\ORMDataProcessor;
use LAG\AdminBundle\Bridge\Doctrine\ORM\State\ORMDataProvider;

class Create extends Operation
{
    public function __construct(
        ?string $name = 'create',
        ?string $title = null,
        ?string $description = null,
        ?string $icon = null,
        ?string $template = '@LAGAdmin/crud/create.html.twig',
        ?array $permissions = [],
        ?string $controller = \LAG\AdminBundle\Controller\Create::class,
        ?string $route = null,
        ?array $routeParameters = null,
        array $methods = ['POST', 'GET'],
        string $path = '/create',
        ?string $targetRoute = null,
        ?array $targetRouteParameters = null,
        array $properties = [],
        ?string $formType = null,
        array $formOptions = [],
        string $processor = ORMDataProcessor::class,
        string $provider = ORMDataProvider::class,
        array $identifiers = [],
        ?array $contextualActions = null,
        ?array $itemActions = null
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
            $targetRoute,
            $targetRouteParameters,
            $properties,
            $formType,
            $formOptions,
            $processor,
            $provider,
            $identifiers,
            $contextualActions,
            $itemActions,
        );
    }
}
