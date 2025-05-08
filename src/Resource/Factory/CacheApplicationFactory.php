<?php

declare(strict_types=1);

namespace LAG\AdminBundle\Resource\Factory;

use LAG\AdminBundle\Metadata\Application;

final class CacheApplicationFactory implements ApplicationFactoryInterface
{
    private array $cache = [];

    public function __construct(
        private readonly ApplicationFactoryInterface $applicationFactory,
    ) {
    }

    public function create(string $applicationName): Application
    {
        if (empty($this->cache[$applicationName])) {
            $this->cache[$applicationName] = $this->applicationFactory->create($applicationName);
        }

        return $this->cache[$applicationName];
    }
}
