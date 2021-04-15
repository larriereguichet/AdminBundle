<?php

namespace LAG\AdminBundle\Twig\Extension;

use LAG\AdminBundle\Menu\Provider\MenuProvider;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

class MenuExtension extends AbstractExtension
{
    private MenuProvider $menuProvider;

    public function __construct(MenuProvider $menuProvider)
    {
        $this->menuProvider = $menuProvider;
    }

    public function getFunctions(): array
    {
        return [new TwigFunction('admin_has_menu', [$this, 'hasMenu'])];
    }

    public function hasMenu(string $name, array $options = []): bool
    {
        return $this->menuProvider->has($name, $options);
    }
}
