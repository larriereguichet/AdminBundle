<?php

namespace LAG\AdminBundle\Admin;

use LAG\AdminBundle\Configuration\ActionConfiguration;

class Action implements ActionInterface
{
    /**
     * @var string
     */
    private $name;

    /**
     * @var ActionConfiguration
     */
    private $configuration;

    /**
     * Action constructor.
     */
    public function __construct(string $name, ActionConfiguration $configuration)
    {
        $this->name = $name;
        $this->configuration = $configuration;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getConfiguration(): ActionConfiguration
    {
        return $this->configuration;
    }
}
