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
     *
     * @param array $menuConfigurations
     * @param bool  $buildResourceMenu
     */
    public function __construct(array $menuConfigurations = [], bool $buildResourceMenu = true)
    {
        $this->menuConfigurations = $menuConfigurations;
        $this->buildResourceMenu = $buildResourceMenu;
    }

    /**
     * @return array
     */
    public function getMenuConfigurations(): array
    {
        return $this->menuConfigurations;
    }

    /**
     * @param array $menuConfigurations
     */
    public function setMenuConfigurations(array $menuConfigurations)
    {
        $this->menuConfigurations = $menuConfigurations;
    }

    /**
     * @return bool
     */
    public function isBuildResourceMenu(): bool
    {
        return $this->buildResourceMenu;
    }
}
