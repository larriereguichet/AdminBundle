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
     *
     * @param array $menuConfigurations
     */
    public function __construct(array $menuConfigurations = [])
    {
        $this->menuConfigurations = $menuConfigurations;
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
}
