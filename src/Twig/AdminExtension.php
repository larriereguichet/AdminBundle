<?php

namespace LAG\AdminBundle\Twig;

use LAG\AdminBundle\Configuration\ApplicationConfiguration;
use LAG\AdminBundle\Configuration\ApplicationConfigurationStorage;
use LAG\AdminBundle\Configuration\MenuItemConfiguration;
use LAG\AdminBundle\Factory\MenuFactory;
use Symfony\Component\Routing\RouterInterface;
use Twig_Extension;
use Twig_SimpleFunction;

class AdminExtension extends Twig_Extension
{
    /**
     * @var ApplicationConfiguration
     */
    private $applicationConfiguration;

    /**
     * @var MenuFactory
     */
    private $menuFactory;

    /**
     * @var \Twig_Environment
     */
    private $twig;

    /**
     * @var RouterInterface
     */
    private $router;

    public function __construct(
        ApplicationConfigurationStorage $applicationConfigurationStorage,
        MenuFactory $menuFactory,
        \Twig_Environment $twig,
        RouterInterface $router
    ) {
        $this->applicationConfiguration = $applicationConfigurationStorage->getConfiguration();
        $this->menuFactory = $menuFactory;
        $this->twig = $twig;
        $this->router = $router;
    }

    public function getFunctions()
    {
        return [
            new Twig_SimpleFunction('admin_config', [$this, 'getApplicationParameter']),
            new Twig_SimpleFunction('admin_menu', [$this, 'getMenu']),
            new Twig_SimpleFunction('admin_menu_action', [$this, 'getMenuAction']),
        ];
    }

    public function getApplicationParameter($name)
    {
        return $this->applicationConfiguration->getParameter($name);
    }

    public function getMenu($name)
    {
        $menu = $this->menuFactory->getMenu($name);

        return $this->twig->render('LAGAdminBundle:Menu:menu.html.twig', [
            'menu' => $menu,
        ]);
    }

    public function getMenuAction(MenuItemConfiguration $configuration)
    {
        if ($configuration->getParameter('url')) {
            return $configuration->getParameter('url');
        }

        if ($configuration->getParameter('admin')) {
            // generate the route name using the configured pattern
            $routeName = str_replace(
                '{admin}',
                strtolower($configuration->getParameter('admin')),
                $this->applicationConfiguration->getParameter('routing_name_pattern')
            );
            $routeName = str_replace(
                '{action}',
                $configuration->getParameter('action'),
                $routeName
            );

            return $this->router->generate($routeName);
        }

        return $this->router->generate($configuration->getParameter('route'));
    }
}
