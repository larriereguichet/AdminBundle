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
     *
     * @param string $name
     * @param array  $configuration
     */
    public function __construct(string $name, array $configuration)
    {
        $this->name = $name;
        $this->configuration = $configuration;
        $this->entityClass = $configuration['entity'];
    }

    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @return array
     */
    public function getConfiguration(): array
    {
        return $this->configuration;
    }

    /**
     * @return string
     */
    public function getEntityClass(): string
    {
        return $this->entityClass;
    }
}
