<?php

namespace LAG\AdminBundle\Resource;

use LAG\AdminBundle\Exception\Exception;

class AdminResource
{
    /**
     * @var string
     */
    protected $name;

    /**
     * @var array
     */
    protected $configuration;

    /**
     * @var string
     */
    protected $entityClass;

    /**
     * Resource constructor.
     */
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
