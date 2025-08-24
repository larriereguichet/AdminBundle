<?php

declare(strict_types=1);

namespace LAG\AdminBundle\Grid\Initializer;

use LAG\AdminBundle\Exception\Exception;
use LAG\AdminBundle\Metadata\Action;
use LAG\AdminBundle\Metadata\CollectionOperationInterface;
use LAG\AdminBundle\Metadata\Grid;
use LAG\AdminBundle\Metadata\Resource;
use LAG\AdminBundle\Resource\Initializer\ActionInitializerInterface;
use Symfony\Component\HttpFoundation\RequestStack;

use function Symfony\Component\String\u;

final readonly class GridInitializer implements GridInitializerInterface
{
    public function __construct(
        private RequestStack $requestStack,
        private ActionInitializerInterface $actionInitializer,
        private array $gridTemplates,
    ) {
    }

    public function initializeGrid(Resource $resource, CollectionOperationInterface $operation, Grid $grid): Grid
    {
        if ($grid->getType() === null) {
            $grid = $grid->withType('table');
        }

        if ($grid->getName() === null) {
            $gridName = u('{resource}.{operation}.grid')
                ->replace('{resource}', $resource->getName())
                ->replace('{operation}', $resource->getName())
            ;
            $grid = $grid->withName($gridName->toString());
        }

        if ($grid->getTemplate() === null) {
            if (!\array_key_exists($grid->getType(), $this->gridTemplates)) {
                throw new Exception('The type of the grid "%s" is not mapped to any templates', $grid->getName());
            }
            $grid = $grid->withTemplate($this->gridTemplates[$grid->getType()]);
        }

        if ($grid->getTranslationDomain() === null) {
            $grid = $grid->withTranslationDomain($resource->getTranslationDomain());
        }

        if ($grid->isSortable() === null) {
            $request = $this->requestStack->getCurrentRequest();
            $grid = $grid->withSortable(true);

            // Sort on sub-request grid is not allowed
            if ($request !== $this->requestStack->getMainRequest()) {
                $grid = $grid->withSortable(false);
            }
        }

        if (!$grid->hasProperties()) {
            $properties = [];

            foreach ($resource->getProperties() as $property) {
                $properties[] = $property->getName();
            }
            $grid = $grid->withProperties($properties);
        }
        $actions = [];

        if ($grid->getActions() === null) {
            if ($resource->hasOperation('show')) {
                $actions[] = new Action(operation: $resource->getOperation('show')->getFullName());
            }

            if ($resource->hasOperation('update')) {
                $actions[] = new Action(
                    attributes: ['class' => 'btn-primary'],
                    operation: $resource->getOperation('update')->getFullName(),
                );
            }

            if ($resource->hasOperation('delete')) {
                $actions[] = new Action(
                    attributes: ['class' => 'btn-danger'],
                    operation: $resource->getOperation('delete')->getFullName(),
                );
            }
            $grid = $grid->withActions($actions);
        }
        $actions = [];

        foreach ($grid->getActions() ?? [] as $action) {
            $actions[] = $this->actionInitializer->initializeAction($operation, $action);
        }
        $collectionActions = [];

        foreach ($grid->getCollectionActions() ?? [] as $action) {
            $collectionActions[] = $this->actionInitializer->initializeAction($operation, $action);
        }

        return $grid
            ->withActions($actions)
            ->withCollectionActions($collectionActions)
        ;
    }
}
