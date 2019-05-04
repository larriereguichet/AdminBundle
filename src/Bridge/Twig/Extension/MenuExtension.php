<?php

namespace LAG\AdminBundle\Bridge\Twig\Extension;

use LAG\AdminBundle\Factory\MenuFactory;
use LAG\AdminBundle\View\ViewInterface;
use Twig\Environment;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

class MenuExtension extends AbstractExtension
{
    /**
     * @var MenuFactory
     */
    private $menuFactory;

    /**
     * @var Environment
     */
    private $environment;

    public function __construct(MenuFactory $menuFactory, Environment $environment)
    {
        $this->menuFactory = $menuFactory;
        $this->environment = $environment;
    }

    public function getFunctions(): array
    {
        return [
            new TwigFunction('admin_menu', [$this, 'getMenu'], ['is_safe' => ['html']]),
            new TwigFunction('admin_has_menu', [$this, 'hasMenu']),
        ];
    }

    /**
     * Render a menu according to given name.
     *
     * @param string             $name
     * @param ViewInterface|null $view
     *
     * @return string
     */
    public function getMenu(string $name, ViewInterface $view = null)
    {
        $menu = $this->menuFactory->getMenu($name);

        return $this->environment->render($menu->get('template'), [
            'menu' => $menu,
            'admin' => $view,
        ]);
    }

    /**
     * Return true if a menu with the given name exists.
     *
     * @param string $name
     *
     * @return bool
     */
    public function hasMenu(string $name): bool
    {
        return $this->menuFactory->hasMenu($name);
    }
}
