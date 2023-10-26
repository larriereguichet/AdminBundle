<?php

declare(strict_types=1);

namespace LAG\AdminBundle\Metadata;

use LAG\AdminBundle\Bridge\Doctrine\ORM\State\ORMDataProcessor;
use LAG\AdminBundle\Bridge\Doctrine\ORM\State\ORMDataProvider;
use LAG\AdminBundle\Controller\Resource\ResourceController;
use LAG\AdminBundle\Form\Type\Resource\DeleteType;

/**
 * The delete operation is used to remove an existing resource. The provider should retrieve the resource, and the
 * processor should delete it
 */
class Delete extends Operation
{
    public function __construct(
        ?string $name = 'delete',
        ?string $title = null,
        ?string $description = null,
        ?string $icon = null,
        ?string $template = '@LAGAdmin/crud/delete.html.twig',
        ?array $permissions = [],
        ?string $controller = ResourceController::class,
        ?string $route = null,
        ?array $routeParameters = null,
        array $methods = ['POST', 'GET'],
        ?string $path = null,
        ?string $redirectRoute = null,
        ?array $redirectRouteParameters = null,
        array $properties = [],
        ?string $formType = DeleteType::class,
        array $formOptions = [],
        string $processor = ORMDataProcessor::class,
        string $provider = ORMDataProvider::class,
        array $identifiers = ['id'],
        ?array $contextualActions = null,
        ?array $itemActions = null,
        ?string $redirectResource = null,
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
            $redirectResource,
            $redirectOperation,
        );
    }
}
