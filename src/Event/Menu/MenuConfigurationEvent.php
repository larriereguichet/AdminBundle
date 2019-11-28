<?php

namespace LAG\AdminBundle\Event\Menu;

use Symfony\Component\EventDispatcher\Event;

class MenuConfigurationEvent extends Event
{
    /**
     * @var array
     */
    private $menuConfigurations;

    /**
     * MenuConfiguration constructor.
     */
    public function __construct(array $menuConfigurations = [])
    {
        $this->menuConfigurations = $menuConfigurations;
    }

    public function getMenuConfigurations(): array
    {
        return $this->menuConfigurations;
    }

    public function setMenuConfigurations(array $menuConfigurations)
    {
        $this->menuConfigurations = $menuConfigurations;
    }
}
