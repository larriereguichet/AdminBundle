<?php

namespace LAG\AdminBundle\Menu;

use Exception;
use Knp\Menu\ItemInterface;
use LAG\AdminBundle\Admin\Request\RequestHandler;
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
     * @var RequestHandler
     */
    protected $requestHandler;

    /**
     * @var RequestStack
     */
    protected $requestStack;

    /**
     * MenuBuilder constructor.
     *
     * @param array $menusConfiguration
     * @param MenuFactory $menuFactory
     * @param RequestHandler $requestHandler
     * @param RequestStack $requestStack
     */
    public function __construct(
        array $menusConfiguration,
        MenuFactory $menuFactory,
        RequestHandler $requestHandler,
        RequestStack $requestStack
    ) {
        $this->menusConfiguration = $menusConfiguration;
        $this->menuFactory = $menuFactory;
        $this->requestHandler = $requestHandler;
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
        $configuration = [];
    
        // request should exists and should have admin parameters
        if ($this->requestHandler->supports($request)) {
            // get current action from admin
            $admin = $this
                ->requestHandler
                ->handle($request)
            ;

            if ($admin->hasView()) {
                // menu configuration
                $menus = $admin
                    ->getView()
                    ->getConfiguration()
                    ->getParameter('menus')
                ;

                if (key_exists('top', $menus)) {
                    //$configuration = $menus['top'];
                    $configuration['attr'] = [
                        //'class' => 'nav navbar-top-links navbar-right in',
                        'class' => 'navbar-nav bd-navbar-nav flex-row',
                    ];
                    $entity = $admin->getView()->getEntities()->first();
                }
            }
        }

        return $this
            ->menuFactory
            ->create('top', $configuration, $entity)
        ;
    }
}
