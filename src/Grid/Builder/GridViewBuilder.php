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
use Symfony\Component\Form\FormView;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Valid;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use function PHPUnit\Framework\matches;

readonly class GridViewBuilder implements GridViewBuilderInterface
{
    public function __construct(
        private GridRegistryInterface $registry,
        private CellBuilderInterface $cellFactory,
        private ResourceEventDispatcherInterface $eventDispatcher,
        private ValidatorInterface $validator,
    ) {
    }

    public function build(
        string $gridName,
        OperationInterface $operation,
        iterable $data,
        ?FormView $form = null,
        array $options = []
    ): GridView {
        $resource = $operation->getResource();
        $grid = $this->registry->get($gridName);
        $event = new GridEvent($grid, $operation);

        $this->eventDispatcher->dispatchNamedEvents(
            $event,
            GridEvents::GRID_CREATE,
            $resource->getApplicationName(),
            $resource->getName(),
            $operation->getName(),
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
        $rows = $this->generateRows($grid, $resource->getProperties(), $data, $form);

        return new GridView(
            name: $grid->getName(),
            type: $grid->getType(),
            template: $grid->getTemplate(),
            headers: $headers,
            rows: $rows,
            attributes: $grid->getAttributes(),
            options: array_merge_recursive($grid->getOptions(), $options),
            form: $form,
        );
    }

    private function generateHeadersRow(Grid $grid, iterable $properties): Row
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
                label: $property->getLabel(),
                sortable: $property->isSortable(),
                translationDomain: $grid->getTranslationDomain(),
                attributes: $grid->getHeaderAttributes(),
            );
        }

        return $headers;
    }

    private function generateRows(Grid $grid, array $properties, iterable $data, ?FormView $form = null): array
    {
        $index = 0;
        $rows = [];

        foreach ($data as $value) {
            $cells = $this->generateCells($grid, $properties, $value);
            $rows[$index] = new Row($index, $cells, $grid->getRowAttributes(), $form);
            $index++;
        }

        return $rows;
    }

    private function generateCells(Grid $grid, array $properties, mixed $data, ?FormView $form = null): array
    {
        $cells = [];

        foreach ($grid->getProperties() as $propertyName) {
            $property = $properties[$propertyName];
            $cells[$property->getName()] = $this->cellFactory->build($property, $data, $form);
        }

        return $cells;
    }
}
