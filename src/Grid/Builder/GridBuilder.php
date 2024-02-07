<?php

declare(strict_types=1);

namespace LAG\AdminBundle\Grid\Builder;

use LAG\AdminBundle\Event\GridEvent;
use LAG\AdminBundle\Event\GridEvents;
use LAG\AdminBundle\EventDispatcher\ResourceEventDispatcherInterface;
use LAG\AdminBundle\Exception\Exception;
use LAG\AdminBundle\Exception\InvalidGridException;
use LAG\AdminBundle\Grid\Registry\GridRegistryInterface;
use LAG\AdminBundle\Grid\View\GridView;
use LAG\AdminBundle\Grid\View\Header;
use LAG\AdminBundle\Grid\View\Row;
use LAG\AdminBundle\Metadata\Grid;
use LAG\AdminBundle\Metadata\OperationInterface;
use LAG\AdminBundle\Resource\DataMapper\ResourceDataMapper;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\Form\FormView;
use Symfony\Component\Validator\Constraints\Valid;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Symfony\Contracts\EventDispatcher\EventDispatcherInterface;

readonly class GridBuilder implements GridBuilderInterface
{
    public function __construct(
        private GridRegistryInterface $registry,
        private CellBuilderInterface $cellBuilder,
        private FormFactoryInterface $formFactory,
        private ResourceEventDispatcherInterface $eventDispatcher,
        private ValidatorInterface $validator,
    ) {
    }

    public function buildView(
        string $gridName,
        OperationInterface $operation,
        iterable $data,
        array $options = []
    ): GridView {
        $resource = $operation->getResource();
        $grid = $this->registry->get($gridName);
        $event = new GridEvent($grid, $operation);

        $this->eventDispatcher->dispatchResourceEvents(
            $event,
            GridEvents::GRID_CREATE,
            $resource->getApplication(),
            $resource->getName(),
            $grid->getName(),
        );
        $grid = $event->getGrid();
        $errors = $this->validator->validate($grid, [new Valid()]);

        if ($errors->count() > 0) {
            throw new InvalidGridException($gridName, $errors);
        }

        if ($grid->getName() !== $gridName) {
            throw new Exception(sprintf('The grid name change from "%s" to "%s" is not allowed', $gridName, $grid->getName()));
        }

        $headers = $this->generateHeadersRow($grid, $resource->getProperties());
        $rows = $this->generateRows($grid, $resource->getProperties(), $data, $operation->getForm(), $operation->getFormOptions());

        return new GridView(
            name: $grid->getName(),
            type: $grid->getType(),
            template: $grid->getTemplate(),
            component: $this->resolveGridType($grid->getType()),
            headers: $headers,
            rows: $rows,
            attributes: $grid->getAttributes(),
            options: array_merge_recursive($grid->getOptions(), $options),
            form: null,
        );
    }

    private function resolveGridType(string $gridType): string
    {
        return match ($gridType) {
            'card' => 'lag_admin:grid:card',
            'table' => 'lag_admin:grid:table',
            default => $gridType,
        };
    }

    private function generateHeadersRow(Grid $grid, array $properties): Row
    {
        return new Row(
            index: 0,
            cells: $this->generateHeaders($grid, $properties),
            attributes: $grid->getHeaderRowAttributes(),
        );
    }

    private function generateHeaders(Grid $grid, array $properties): array
    {
        $headers = [];

        foreach ($grid->getProperties() as $propertyName) {
            if (!array_key_exists($propertyName, $properties)) {
                throw new Exception(sprintf(
                    'The field "%s" of the grid "%s" does not exists in the resource',
                    $propertyName,
                    $grid->getName(),
                ));
            }
            $property = $properties[$propertyName];
            $headers[$property->getName()] = new Header(
                name: $property->getName(),
                text: $property->getLabel() ?? '',
                sortable: $property->isSortable(),
                translationDomain: $grid->getTranslationDomain(),
                attributes: $grid->getHeaderAttributes(),
            );
        }

        return $headers;
    }

    private function generateRows(
        Grid $grid,
        array $properties,
        iterable $data,
        ?string $formType,
        array $formOptions,
    ): array {
        $index = 0;
        $rows = [];

        foreach ($data as $value) {
            $form = null;

            if ($formType) {
                $form = $this->formFactory->create($formType, $value, $formOptions);
            }
            $cells = $this->generateCells($grid, $properties, $value);

            $rows[$index] = new Row($index, $cells, $grid->getRowAttributes(), $form?->createView());

            $index++;
        }

        return $rows;
    }

    private function generateCells(Grid $grid, array $properties, mixed $data): array
    {
        $cells = [];

        foreach ($grid->getProperties() as $propertyName) {
            $property = $properties[$propertyName];
            $cells[$property->getName()] = $this->cellBuilder->build($property, $data);
        }

        return $cells;
    }
}
