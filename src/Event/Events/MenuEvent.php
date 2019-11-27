<?php

namespace LAG\AdminBundle\Event\Events;

use Symfony\Component\EventDispatcher\Event;

class MenuEvent extends Event
{
    /**
     * @var array
     */
    private $menuConfigurations;

    /**
     * @var bool
     */
    private $buildResourceMenu;

    /**
     * MenuEvent constructor.
     */
    public function __construct(array $menuConfigurations = [], bool $buildResourceMenu = true)
    {
        $this->menuConfigurations = $menuConfigurations;
        $this->buildResourceMenu = $buildResourceMenu;
    }

    public function getMenuConfigurations(): array
    {
        return $this->menuConfigurations;
    }

    public function setMenuConfigurations(array $menuConfigurations)
    {
        $this->menuConfigurations = $menuConfigurations;
    }

    public function isBuildResourceMenu(): bool
    {
        return $this->buildResourceMenu;
    }
}
