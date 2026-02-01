<?php

declare(strict_types=1);

namespace LAG\AdminBundle\Grid\Factory;

use LAG\AdminBundle\Exception\InvalidGridException;
use LAG\AdminBundle\Metadata\CollectionOperationInterface;
use LAG\AdminBundle\Metadata\Factory\GridMetadataFactoryInterface;
use LAG\AdminBundle\Metadata\GridInterface;
use LAG\AdminBundle\Grid\Initializer\GridInitializerInterface;
use Symfony\Component\Validator\Constraints\Valid;
use Symfony\Component\Validator\Validator\ValidatorInterface;

final readonly class GridFactory implements GridFactoryInterface
{
    public function __construct(
        private GridMetadataFactoryInterface $metadataFactory,
        private GridInitializerInterface $gridInitializer,
        private ValidatorInterface $validator,
    ) {
    }

    public function create(string $gridName, CollectionOperationInterface $operation): GridInterface
    {
        $grid = $this->metadataFactory->create($gridName);
        $grid = $this->gridInitializer->initializeGrid($operation->getResource(), $operation, $grid);
        $errors = $this->validator->validate($grid, [new Valid()]);

        if ($errors->count() > 0) {
            throw new InvalidGridException($gridName, $errors);
        }

        return $grid;
    }
}
