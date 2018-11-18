<?php

namespace LAG\AdminBundle\Menu;

use LAG\AdminBundle\Configuration\MenuConfiguration;

class Menu
{
    /**
     * @var MenuItem[]
     */
    private $items = [];

    /**
     * @var string
     */
    private $name;

    /**
     * @var MenuConfiguration
     */
    private $configuration;

    /**
     * Menu constructor.
     *
     * @param string            $name
     * @param MenuConfiguration $configuration
     */
    public function __construct(string $name, MenuConfiguration $configuration)
    {
        $this->name = $name;
        $this->configuration = $configuration;
    }

    public function addItem(MenuItem $item)
    {
        $this->items[] = $item;
    }

    /**
     * @return MenuItem[]
     */
    public function getItems(): array
    {
        return $this->items;
    }

    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
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
