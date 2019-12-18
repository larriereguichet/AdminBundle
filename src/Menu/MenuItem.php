<?php

namespace LAG\AdminBundle\Menu;

use LAG\AdminBundle\Configuration\MenuItemConfiguration;

class MenuItem
{
    /**
     * @var MenuItemConfiguration
     */
    private $configuration;

    /**
     * MenuItem constructor.
     */
    public function __construct(MenuItemConfiguration $configuration)
    {
        $this->configuration = $configuration;
    }

    public function getConfiguration(): MenuItemConfiguration
    {
        return $this->configuration;
    }

    /**
     * @return mixed
     */
    public function get(string $parameter)
    {
        return $this->configuration->get($parameter);
    }

    public function getName(): string
    {
        return $this->configuration->getName();
    }

    public function getPosition(): ?string
    {
        return $this->configuration->getPosition();
    }
}
