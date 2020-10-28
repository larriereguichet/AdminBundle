<?php

namespace LAG\AdminBundle\Admin\Resource;

use LAG\AdminBundle\Exception\Exception;

class AdminResource
{
    protected string $name;
    protected array $configuration;
    protected $entityClass;

    public function __construct(string $name, array $configuration)
    {
        $this->name = $name;
        $this->configuration = $configuration;

        if (!key_exists('entity', $configuration)) {
            throw new Exception(sprintf('The configuration of the resource %s is not well formed', $name));
        }
        $this->entityClass = $configuration['entity'];
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getConfiguration(): array
    {
        return $this->configuration;
    }

    public function getEntityClass(): string
    {
        return $this->entityClass;
    }
}
