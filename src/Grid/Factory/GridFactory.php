<?php

declare(strict_types=1);

namespace LAG\AdminBundle\Grid\Factory;

use LAG\AdminBundle\Event\GridEvent;
use LAG\AdminBundle\Event\GridEvents;
use LAG\AdminBundle\EventDispatcher\ResourceEventDispatcherInterface;
use LAG\AdminBundle\Exception\InvalidGridException;
use LAG\AdminBundle\Grid\Builder\GridBuilderInterface;
use LAG\AdminBundle\Metadata\Grid;
use LAG\AdminBundle\Metadata\OperationInterface;
use LAG\AdminBundle\Resource\Factory\DefinitionFactoryInterface;
use Symfony\Component\Validator\Constraints\Valid;
use Symfony\Component\Validator\Validator\ValidatorInterface;

final readonly class GridFactory implements GridFactoryInterface
{
    /** @param iterable<GridBuilderInterface> $builders */
    public function __construct(
        private DefinitionFactoryInterface $definitionFactory,
        private ResourceEventDispatcherInterface $eventDispatcher,
        private ValidatorInterface $validator,
        private iterable $builders,
    ) {
    }

    public function createGrid(string $gridName, OperationInterface $operation): Grid
    {
        $grid = null;

        foreach ($this->builders as $builder) {
            if ($builder->getGridName() === $gridName) {
                $grid = $builder->buildGrid();
            }
        }

        if ($grid === null) {
            $grid = $this->definitionFactory->createGridDefinition($gridName);
        }
        $event = new GridEvent($grid, $operation);
        $this->eventDispatcher->dispatchBuildEvents($event, GridEvents::GRID_EVENT_TEMPLATE);
        $grid = $event->getGrid();

        $errors = $this->validator->validate($grid, [new Valid()]);

        if ($errors->count() > 0) {
            throw new InvalidGridException($gridName, $errors);
        }

        return $grid;
    }
}
