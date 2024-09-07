<?php

declare(strict_types=1);

namespace LAG\AdminBundle\Grid\ViewBuilder;

use LAG\AdminBundle\Exception\Exception;
use LAG\AdminBundle\Grid\View\CellView;
use LAG\AdminBundle\Resource\DataMapper\DataMapperInterface;
use LAG\AdminBundle\Resource\Metadata\Collection;
use LAG\AdminBundle\Resource\Metadata\Grid;
use LAG\AdminBundle\Resource\Metadata\PropertyInterface;

final readonly class CollectionCellViewBuilder implements CellViewBuilderInterface
{
    public function __construct(
        private CellViewBuilderInterface $cellBuilder,
        private DataMapperInterface $dataMapper,
    ) {
    }

    public function buildCell(Grid $grid, PropertyInterface $property, mixed $data, array $context = []): CellView
    {
        if (!$property instanceof Collection) {
            return $this->cellBuilder->buildCell($grid, $property, $data, $context);
        }

        if (!is_iterable($data)) {
            throw new Exception(\sprintf('The collection property "%s" requires iterable data, got "%s"', $property->getName(), get_debug_type($data)));
        }
        $context['children'] = [];
        $index = 0;

        foreach ($data as $propertyData) {
            $child = $property
                ->getEntryProperty()
                ->withName($property->getName().'_'.$index)
            ;
            $propertyData = $this->dataMapper->getValue($child, $propertyData);

            $context['children'][] = $this->cellBuilder->buildCell(
                $grid,
                $child,
                $propertyData,
            );
            ++$index;
        }

        return $this->cellBuilder->buildCell($grid, $property, $data, $context);
    }
}
