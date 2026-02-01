<?php

declare(strict_types=1);

namespace LAG\AdminBundle\Metadata\Factory;

use LAG\AdminBundle\Grid\Provider\GridProviderInterface;
use LAG\AdminBundle\Metadata\GridInterface;

final readonly class GridProviderMetadataFactory implements GridMetadataFactoryInterface
{
    public function __construct(
        /** @var iterable<GridProviderInterface> $builders */
        private iterable $builders,
        private GridMetadataFactoryInterface $metadataFactory,
    ) {
    }

    public function create(string $gridName): GridInterface
    {
        foreach ($this->builders as $builder) {
            if ($builder->supports($gridName)) {
                return $builder->provide($gridName);
            }
        }

        return $this->metadataFactory->create($gridName);
    }
}
