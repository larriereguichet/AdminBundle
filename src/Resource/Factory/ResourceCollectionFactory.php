<?php

declare(strict_types=1);

namespace LAG\AdminBundle\Resource\Factory;

final readonly class ResourceCollectionFactory implements ResourceCollectionFactoryInterface
{
    /**
     * @param array<string, array<string, mixed>> $resources
     */
    public function __construct(
        private array $resources,
        private ResourceFactoryInterface $resourceFactory,
    ) {
    }

    public function create(): array
    {
        $resources = [];

        foreach (array_keys($this->resources) as $resourceName) {
            $resources[] = $this->resourceFactory->create($resourceName);
        }

        return $resources;
    }
}
