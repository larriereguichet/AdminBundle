<?php

namespace LAG\AdminBundle\Resource;

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
