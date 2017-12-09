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
     *
     * @param                     $name
     * @param ActionConfiguration $configuration
     */
    public function __construct($name, ActionConfiguration $configuration)
    {
        $this->name = $name;
        $this->configuration = $configuration;
    }

    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @return ActionConfiguration
     */
    public function getConfiguration(): ActionConfiguration
    {
        return $this->configuration;
    }
}
