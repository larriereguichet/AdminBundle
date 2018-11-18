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
     *
     * @param MenuItemConfiguration $configuration
     */
    public function __construct(MenuItemConfiguration $configuration)
    {
        $this->configuration = $configuration;
    }

    /**
     * @return MenuItemConfiguration
     */
    public function getConfiguration(): MenuItemConfiguration
    {
        return $this->configuration;
    }

    /**
     * @param string $parameter
     *
     * @return mixed
     */
    public function get(string $parameter)
    {
        return $this->configuration->getParameter($parameter);
    }
}
