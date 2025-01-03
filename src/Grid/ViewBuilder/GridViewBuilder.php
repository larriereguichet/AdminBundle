<?php

declare(strict_types=1);

namespace LAG\AdminBundle\Grid\ViewBuilder;

use LAG\AdminBundle\Event\GridEvent;
use LAG\AdminBundle\Event\GridEvents;
use LAG\AdminBundle\EventDispatcher\ResourceEventDispatcherInterface;
use LAG\AdminBundle\Exception\Exception;
use LAG\AdminBundle\Exception\InvalidGridException;
use LAG\AdminBundle\Grid\View\GridView;
use LAG\AdminBundle\Grid\View\RowView;
use LAG\AdminBundle\Resource\Metadata\Grid;
use LAG\AdminBundle\Resource\Metadata\OperationInterface;
use Symfony\Component\Validator\Constraints\Valid;
use Symfony\Component\Validator\Validator\ValidatorInterface;

final readonly class GridViewBuilder implements GridViewBuilderInterface
{
    public function __construct(
        private RowViewBuilderInterface $rowBuilder,
        private ActionViewBuilderInterface $actionBuilder,
        private ResourceEventDispatcherInterface $eventDispatcher,
        private ValidatorInterface $validator,
    ) {
    }

    public function build(OperationInterface $operation, Grid $grid, mixed $data, array $context = []): GridView
    {
        $event = new GridEvent($grid, $operation);
        $resource = $operation->getResource();

        $this->eventDispatcher->dispatchEvents(
            $event,
            GridEvents::GRID_BUILD_PATTERN,
            $resource->getApplication(),
            $resource->getName(),
            null,
            $grid->getName(),
        );
        $grid = $event->getGrid();
        $errors = $this->validator->validate($grid, [new Valid()]);

        if ($errors->count() > 0) {
            throw new InvalidGridException($grid->getName() ?? '', $errors);
        }

        return new GridView(
            name: $grid->getName(),
            type: $grid->getType(),
            headers: $this->buildHeaders($operation, $grid, $context),
            rows: $this->buildRows($operation, $grid, $data, $context),
            attributes: $grid->getAttributes(),
            title: $grid->getTitle(),
            template: $grid->getTemplate(),
            component: $grid->getComponent(),
            options: $grid->getOptions(),
            actions: $this->buildCollectionActions($grid, $data),
            context: $context,
            containerAttributes: $grid->getContainerAttributes(),
            actionCellAttributes: $grid->getActionCellAttributes(),
            extraColumn: \count($grid->getActions()) > 0,
            emptyMessage: $grid->getEmptyMessage(),
            translationDomain: $grid->getTranslationDomain(),
        );
    }

    private function buildHeaders(OperationInterface $operation, Grid $grid, array $context): RowView
    {
        return $this->rowBuilder->buildHeadersRow($operation, $grid, $context);
    }

    private function buildRows(OperationInterface $operation, Grid $grid, mixed $data, array $context): iterable
    {
        if (!is_iterable($data)) {
            throw new Exception('Data must be iterable to build a grid.');
        }
        $rows = [];

        foreach ($data as $row) {
            $rows[] = $this->rowBuilder->buildRow($operation, $grid, $row, $context);
        }

        return $rows;
    }

    private function buildCollectionActions(Grid $grid, mixed $data): array
    {
        $actions = [];

        foreach ($grid->getCollectionActions() as $action) {
            $action = $this->actionBuilder->buildActions($action, $data);

            if ($action !== null) {
                $actions[] = $action;
            }
        }

        return $actions;
    }
}
