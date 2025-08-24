<?php

declare(strict_types=1);

namespace LAG\AdminBundle\Grid\Factory;

use LAG\AdminBundle\Exception\InvalidGridException;
use LAG\AdminBundle\Grid\Initializer\GridInitializerInterface;
use LAG\AdminBundle\Grid\Provider\GridProviderInterface;
use LAG\AdminBundle\Metadata\CollectionOperationInterface;
use LAG\AdminBundle\Metadata\Grid;
use LAG\AdminBundle\Resource\Factory\DefinitionFactoryInterface;
use Symfony\Component\Validator\Constraints\Valid;
use Symfony\Component\Validator\Validator\ValidatorInterface;

final readonly class GridFactory implements GridFactoryInterface
{
    /** @param iterable<GridProviderInterface> $builders */
    public function __construct(
        private DefinitionFactoryInterface $definitionFactory,
        private GridInitializerInterface $gridInitializer,
        private ValidatorInterface $validator,
        private iterable $builders,
    ) {
    }

    public function createGrid(CollectionOperationInterface $operation): Grid
    {
        $grid = null;

        foreach ($this->builders as $builder) {
            if ($builder->supports($operation)) {
                $grid = $builder->getGrid($operation);
            }
        }

        if ($grid === null) {
            $grid = $this->definitionFactory->createGridDefinition($operation->getGrid());
        }
        $grid = $this->gridInitializer->initializeGrid($operation->getResource(), $operation, $grid);
        $errors = $this->validator->validate($grid, [new Valid()]);

        if ($errors->count() > 0) {
            throw new InvalidGridException($operation->getGrid(), $errors);
        }

        return $grid;
    }
}
