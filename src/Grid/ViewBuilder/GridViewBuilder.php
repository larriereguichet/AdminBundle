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
use LAG\AdminBundle\Resource\DataMapper\DataMapperInterface;
use LAG\AdminBundle\Resource\DataMapper\ResourceDataMapper;
use LAG\AdminBundle\Resource\Metadata\Collection;
use LAG\AdminBundle\Resource\Metadata\CompoundPropertyInterface;
use LAG\AdminBundle\Resource\Metadata\Grid;
use LAG\AdminBundle\Resource\Metadata\OperationInterface;
use LAG\AdminBundle\Resource\Metadata\Resource;
use Symfony\Component\Validator\Constraints\Valid;
use Symfony\Component\Validator\Validator\ValidatorInterface;

final readonly class GridViewBuilder implements GridViewBuilderInterface
{
    public function __construct(
        private CellViewBuilderInterface $cellBuilder,
        private ActionViewBuilderInterface $actionBuilder,
        private ResourceEventDispatcherInterface $eventDispatcher,
        private ValidatorInterface $validator,
        private DataMapperInterface $dataMapper,
    ) {
    }

    public function build(Grid $grid, OperationInterface $operation, mixed $data, array $context = []): GridView
    {
        $event = new GridEvent($grid, $operation);
        $resource = $operation->getResource();

        $this->eventDispatcher->dispatchResourceEvents(
            $event,
            GridEvents::GRID_BUILD,
            $resource->getApplication(),
            $resource->getName(),
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
            headers: $this->buildHeaders($grid, $resource, $context),
            rows: $this->buildRows($grid, $resource, $data, $context),
            attributes: $grid->getAttributes(),
            title: $grid->getTitle(),
            template: $grid->getTemplate(),
            component: $grid->getComponent(),
            context: $context,
            actions: $this->buildCollectionActions($grid, $data),
            options: $grid->getOptions(),
            extraColumn: count($grid->getActions()) > 0,
        );
    }

    private function buildHeaders(Grid $grid, Resource $resource, array $context): iterable
    {
        $headers = [];

        foreach ($grid->getProperties() as $propertyName) {
            $property = $resource->getProperty($propertyName);

            $headers[] = $this->cellBuilder->buildHeader($grid, $property, $context);
        }

        return $headers;
    }

    private function buildRows(Grid $grid, Resource $resource, mixed $data, array $context): iterable
    {
        if (!is_iterable($data)) {
            throw new Exception('Data must be iterable.');
        }
        $rows = [];

        foreach ($data as $row) {
            $rows[] = new RowView(
                cells: $this->buildCells($grid, $resource, $row, $context),
                actions: $this->buildActions($grid, $row),
                attributes: $grid->getRowAttributes(),
            );
        }

        return $rows;
    }

    private function buildCells(Grid $grid, Resource $resource, mixed $data, array $context): iterable
    {
        $cells = [];

        foreach ($grid->getProperties() as $propertyName) {
            $property = $resource->getProperty($propertyName);
            $cellData = $this->dataMapper->getValue($property, $data);

            if ($property instanceof CompoundPropertyInterface && empty($context['children'])) {
                $context['children'] = [];

                foreach ($property->getProperties() as $childPropertyName) {
                    $child = $resource->getProperty($childPropertyName);
                    $childData = $this->dataMapper->getValue($child, $data);
                    $context['children'][] = $this->cellBuilder->build($grid, $child, $childData);
                }
            }

            if ($property instanceof Collection) {
                if (!is_iterable($cellData)) {
                    throw new Exception(sprintf('The collection property "%s" requires iterable data', $property->getName()));
                }
                $context['children'] = [];

                foreach ($cellData as $index => $childData) {
                    $childProperty = $property->getEntryProperty()
                        ->withName($property->getName().'_'.$index)
                    ;
                    $childData = $this->dataMapper->getValue($childProperty, $childData);

                    $context['children'][] = $this->cellBuilder->build(
                        $grid,
                        $childProperty,
                        $childData,
                    );
                }
            }
            $cells[] = $this->cellBuilder->build($grid, $property, $cellData, $context);
        }

        return $cells;
    }

    private function buildActions(Grid $grid, mixed $data): iterable
    {
        $actions = [];

        foreach ($grid->getActions() as $action) {
            $action = $this->actionBuilder->buildActions($action, $data);

            if ($action !== null) {
                $actions[] = $action;
            }
        }

        return $actions;
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
