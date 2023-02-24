<?php

declare(strict_types=1);

namespace LAG\AdminBundle\Metadata\Factory;

use LAG\AdminBundle\Metadata\AdminResource;

class ResourceFactoryCacheDecorator implements ResourceFactoryInterface
{
    private array $cache = [];

    public function __construct(
        private ResourceFactoryInterface $decorated,
    ) {
    }

    public function create(AdminResource $definition): AdminResource
    {
        $resourceName = $definition->getName();

        if (!\array_key_exists($resourceName, $this->cache)) {
            $this->cache[$resourceName] = $this->decorated->create($definition);
        }

        return $this->cache[$resourceName];
    }
}
