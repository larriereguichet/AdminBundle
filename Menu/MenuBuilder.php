<?php

namespace LAG\AdminBundle\Menu;

use Knp\Menu\ItemInterface;
use LAG\AdminBundle\Admin\Factory\AdminFactory;
use LAG\AdminBundle\Menu\Factory\MenuFactory;
use Symfony\Component\HttpFoundation\RequestStack;

/**
 * Create menu handled by the AdminBundle (main, top)
 */
class MenuBuilder
{
    /**
     * @var array
     */
    protected $menusConfiguration;

    /**
     * @var MenuFactory
     */
    protected $menuFactory;

    /**
     * @var AdminFactory
     */
    protected $adminFactory;

    /**
     * @var RequestStack
     */
    protected $requestStack;

    /**
     * MenuBuilder constructor.
     *
     * @param array $menusConfiguration
     * @param MenuFactory $menuFactory
     * @param AdminFactory $adminFactory
     * @param RequestStack $requestStack
     */
    public function __construct(
        array $menusConfiguration,
        MenuFactory $menuFactory,
        AdminFactory $adminFactory,
        RequestStack $requestStack
    ) {
        $this->menusConfiguration = $menusConfiguration;
        $this->menuFactory = $menuFactory;
        $this->adminFactory = $adminFactory;
        $this->requestStack = $requestStack;
    }

    /**
     * Create main menu.
     *
     * @return ItemInterface
     */
    public function mainMenu()
    {
        if (!array_key_exists('main', $this->menusConfiguration)) {
            $this->menusConfiguration['main'] = [];
        }
        $this->menusConfiguration['main']['attr'] = [
            'id' => 'side-menu',
            'class' => 'nav in',
        ];

        return $this
            ->menuFactory
            ->create('main', $this->menusConfiguration['main']);
    }

    /**
     * Create dynamic top menu.
     *
     * @return ItemInterface
     * @throws \Exception
     */
    public function topMenu()
    {
        // get current request
        $request = $this
            ->requestStack
            ->getCurrentRequest();
        $menusConfiguration = [];

        if ($request === null || empty($request->get('_route_params')['_admin'])) {
            $menusConfiguration['top'] = [];
        } else {
            // get current action from admin
            $action = $this
                ->adminFactory
                ->getAdminFromRequest($request)
                ->getCurrentAction();

            // menu configuration
            $menusConfiguration = $action
                ->getConfiguration()
                ->getParameter('menus');

            if (!array_key_exists('top', $menusConfiguration)) {
                $menusConfiguration['top'] = [];
            }
            $menusConfiguration['top']['attr'] = [
                'class' => 'nav navbar-top-links navbar-right in',
            ];
        }

        return $this
            ->menuFactory
            ->create('top', $menusConfiguration['top']);
    }
}
