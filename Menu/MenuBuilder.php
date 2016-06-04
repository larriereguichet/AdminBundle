<?php

namespace LAG\AdminBundle\Menu;

use Exception;
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
     * @param array $options
     * @return ItemInterface
     */
    public function mainMenu(array $options = [])
    {
        if (!array_key_exists('main', $this->menusConfiguration)) {
            $this->menusConfiguration['main'] = [];
        }
        $this->menusConfiguration['main']['attr'] = [
            'id' => 'side-menu',
            'class' => 'nav in',
        ];
        $entity = null;

        if (array_key_exists('entity', $options)) {
            $entity = $options['entity'];
        }

        return $this
            ->menuFactory
            ->create('main', $this->menusConfiguration['main'], $entity);
    }

    /**
     * Create dynamic top menu.
     *
     * @param array $options
     * @return ItemInterface
     * @throws Exception
     */
    public function topMenu(array $options = [])
    {
        // get current request
        $request = $this
            ->requestStack
            ->getCurrentRequest();
        $entity = null;
        $menusConfiguration = [];
        $menusConfiguration['top'] = [];

        // request should exists and should have admin parameters
        if ($request !== null && !empty($request->get('_route_params')['_admin'])) {
            // get current action from admin
            $admin = $this
                ->adminFactory
                ->getAdminFromRequest($request);

            if ($admin->isCurrentActionDefined()) {
                // menu configuration
                $menusConfiguration = $admin
                    ->getCurrentAction()
                    ->getConfiguration()
                    ->getParameter('menus');

                if (!array_key_exists('top', $menusConfiguration)) {
                    $menusConfiguration['top'] = [];
                }
                $menusConfiguration['top']['attr'] = [
                    'class' => 'nav navbar-top-links navbar-right in',
                ];
            }
        }

        if (array_key_exists('entity', $options)) {
            $entity = $options['entity'];
        }

        return $this
            ->menuFactory
            ->create('top', $menusConfiguration['top'], $entity);
    }
}
