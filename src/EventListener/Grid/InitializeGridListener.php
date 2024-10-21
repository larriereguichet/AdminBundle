<?php

declare(strict_types=1);

namespace LAG\AdminBundle\EventListener\Grid;

use LAG\AdminBundle\Event\GridEvent;
use LAG\AdminBundle\Form\Type\Data\HiddenDataType;
use LAG\AdminBundle\Resource\Metadata\Action;
use LAG\AdminBundle\Resource\Metadata\Delete;
use LAG\AdminBundle\Resource\Metadata\Grid;
use LAG\AdminBundle\Resource\Metadata\OperationInterface;
use LAG\AdminBundle\Resource\Metadata\Show;
use LAG\AdminBundle\Resource\Metadata\Update;
use Symfony\Component\HttpFoundation\RequestStack;

use function Symfony\Component\String\u;

final readonly class InitializeGridListener
{
    public function __construct(
        private RequestStack $requestStack,
    ) {
    }

    public function __invoke(GridEvent $event): void
    {
        $grid = $event->getGrid();
        $operation = $event->getOperation();
        $resource = $event->getResource();

        if ($grid->getType() === null) {
            $grid = $grid->withType('table');
        }

        if ($grid->getComponent() === null && $grid->getTemplate() === null) {
            if ($grid->getType() === 'card') {
                $grid = $grid->withTemplate('@LAGAdmin/grids/card.html.twig');
            }

            if ($grid->getType() === 'table') {
                $grid = $grid->withTemplate('@LAGAdmin/grids/table.html.twig');
            }
        }

        if ($grid->getHeaderTemplate() === null) {
            if ($grid->getType() === 'table') {
                $grid = $grid->withHeaderTemplate('@LAGAdmin/grids/table/header.html.twig');
            }
        }

        if ($grid->getName() === null) {
            $gridName = u('{resource}.{operation}.grid')
                ->replace('{resource}', $resource->getName())
                ->replace('{operation}', $resource->getName())
            ;
            $grid = $grid->withName($gridName->toString());
        }

        if ($grid->getForm() === HiddenDataType::class) {
            $grid = $grid->withFormOptions([
                'application' => $resource->getApplication(),
                'resource' => $resource->getName(),
            ]);
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
            if ($resource->hasOperationOfType(Show::class)) {
                $actions[] = new Action(operation: $resource->getOperationOfType(Show::class)->getName());
            }

            if ($resource->hasOperationOfType(Update::class)) {
                $actions[] = new Action(
                    attributes: ['class' => 'btn-primary'],
                    operation: $resource->getOperationOfType(Update::class)->getName(),
                );
            }

            if ($resource->hasOperationOfType(Delete::class)) {
                $actions[] = new Action(
                    attributes: ['class' => 'btn-danger'],
                    operation: $resource->getOperationOfType(Delete::class)->getName(),
                );
            }
            $grid = $grid->withActions($actions);
        }
        $actions = [];

        foreach ($grid->getActions() ?? [] as $action) {
            $actions[] = $this->initializeAction($action, $operation, $grid);
        }
        $collectionActions = [];

        foreach ($grid->getCollectionActions() ?? [] as $action) {
            $collectionActions[] = $this->initializeAction($action, $operation, $grid);
        }
        $grid = $grid->withActions($actions)->withCollectionActions($collectionActions);

        $event->setGrid($grid);
    }

    private function initializeAction(Action $action, OperationInterface $operation, Grid $grid): Action
    {
        $resource = $operation->getResource();

        if ($action->getName() === null) {
            /** @var Action $action */
            $action = $action->withName($action->getOperation());
        }

        if ($action->getResource() === null) {
            /** @var Action $action */
            $action = $action->withResource($operation->getResource()->getName());
        }

        if ($action->getApplication() === null) {
            /** @var Action $action */
            $action = $action->withApplication($operation->getResource()->getApplication());
        }

        if ($action->getLabel() === null) {
            if ($grid->getTranslationDomain()) {
                /** @var Action $action */
                $action = $action->withLabel(
                    u($operation->getResource()->getTranslationPattern() ?? '{application}.{resource}.{message}')
                        ->replace('{application}', $resource->getApplication())
                        ->replace('{resource}', $resource->getName())
                        ->replace('{operation}', $action->getOperation())
                        ->replace('{message}', $action->getOperation())
                        ->toString()
                );
            } else {
                /** @var Action $action */
                $action = $action->withLabel(u($operation->getName())->title()->toString());
            }
        }

        if ($action->getTranslationDomain() === null && $action->isTranslatable()) {
            /** @var Action $action */
            $action = $action->withTranslationDomain($operation->getResource()->getTranslationDomain());
        }

        return $action;
    }
}
