<?php

namespace LAG\AdminBundle\Menu\Factory;

use Exception;
use Knp\Menu\FactoryInterface;
use Knp\Menu\ItemInterface;
use LAG\AdminBundle\Admin\Behaviors\TranslationKeyTrait;
use LAG\AdminBundle\Admin\Factory\AdminFactory;
use LAG\AdminBundle\Configuration\Factory\ConfigurationFactory;
use LAG\AdminBundle\Menu\Configuration\MenuConfiguration;
use LAG\AdminBundle\Menu\Configuration\MenuItemConfiguration;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\PropertyAccess\PropertyAccess;
use Symfony\Component\Translation\TranslatorInterface;

/**
 * Create KNP menus from given options.
 */
class MenuFactory
{
    use TranslationKeyTrait;

    /**
     * @var AdminFactory
     */
    protected $adminFactory;

    /**
     * @var FactoryInterface
     */
    protected $menuFactory;

    /**
     * @var ConfigurationFactory
     */
    protected $configurationFactory;

    /**
     * @var TranslatorInterface
     */
    protected $translator;

    /**
     * MenuBuilder constructor.
     *
     * @param FactoryInterface $menuFactory
     * @param AdminFactory $adminFactory
     * @param ConfigurationFactory $configurationFactory
     * @param TranslatorInterface $translator
     */
    public function __construct(
        FactoryInterface $menuFactory,
        AdminFactory $adminFactory,
        ConfigurationFactory $configurationFactory,
        TranslatorInterface $translator
    ) {
        $this->adminFactory = $adminFactory;
        $this->menuFactory = $menuFactory;
        $this->configurationFactory = $configurationFactory;
        $this->translator = $translator;
    }

    /**
     * Create a menu according to the given configuration.
     *
     * @param string $name
     * @param array $configuration
     * @param null $entity
     * @return ItemInterface
     */
    public function create($name, array $configuration, $entity = null)
    {
        // resolve menu configuration. It will return a MenuConfiguration with MenuItemConfiguration object
        $resolver = new OptionsResolver();
        $menuConfiguration = new MenuConfiguration();
        $menuConfiguration->configureOptions($resolver);
        $menuConfiguration->setParameters($resolver->resolve($configuration));

        // create knp menu
        $menu = $this
            ->menuFactory
            ->createItem($name, [
                'childrenAttributes' => $menuConfiguration->getParameter('attr')
            ]);

        /**
         * @var string $itemName
         * @var MenuItemConfiguration $itemConfiguration
         */
        foreach ($menuConfiguration->getParameter('items') as $itemName => $itemConfiguration) {
            // guess item text from configuration
            $text = $this->guessItemText($itemName, $itemConfiguration);

            // item children
            $subItems = $itemConfiguration->getParameter('items');
            $hasChildren = count($subItems);

            // create menu item configuration (link attributes, icon, route...)
            $menuItemConfiguration = $this->createMenuItemConfiguration($itemConfiguration, $entity);

            if ($hasChildren) {
                $menuItemConfiguration['childrenAttributes']['class'] = 'nav nav-second-level collapse';
            }

            // add to knp menu
            $menu->addChild($text, $menuItemConfiguration);

            if ($hasChildren) {

                foreach ($subItems as $subItemName => $subItemConfiguration) {
                    $subMenuItemConfiguration = $this->createMenuItemConfiguration($subItemConfiguration);

                    $menu[$text]->addChild(
                        $this->guessItemText($subItemName, $subItemConfiguration),
                        $subMenuItemConfiguration
                    );
                }
            }
        }

        return $menu;
    }

    /**
     * Guess an item text according to the configuration.
     *
     * @param string $name
     * @param MenuItemConfiguration $itemConfiguration
     * @return string
     */
    protected function guessItemText($name, MenuItemConfiguration $itemConfiguration)
    {
        $text = $itemConfiguration->getParameter('text');

        if ($text) {
            return $text;
        }

        // if an admin is defined, we get the text using the translation pattern and the admin action's name
        if ($admin = $itemConfiguration->getParameter('admin')) {
            $translationPattern = $this
                ->configurationFactory
                ->getApplicationConfiguration()
                ->getParameter('translation')['pattern'];
            $action = $itemConfiguration->getParameter('action');

            $text = $this->getTranslationKey($translationPattern, $action, $admin);
        } else {
            $text = $name;
        }

        return $text;
    }


    /**
     * Create a knp menu item configuration from the configured options.
     *
     * @param MenuItemConfiguration $itemConfiguration
     * @param null $entity
     * @return array
     * @throws Exception
     */
    protected function createMenuItemConfiguration(MenuItemConfiguration $itemConfiguration, $entity = null)
    {
        $menuItemConfiguration = [
            // configured <a> tag attributes
            'linkAttributes' => $itemConfiguration->getParameter('attr'),
            'routeParameters' => $itemConfiguration->getParameter('parameters'),
            'attributes' => [
                'icon' => $itemConfiguration->getParameter('icon'),
            ]
        ];

        // add parameters value from entity
        if ($entity && count($menuItemConfiguration['routeParameters'])) {
            $routeParameters = [];

            foreach ($menuItemConfiguration['routeParameters'] as $name => $value) {

                if ($value === null) {
                    // get value from current entity
                    $propertyAccessor = PropertyAccess::createPropertyAccessor();
                    $routeParameters[$name] = $propertyAccessor->getValue($entity, $name);
                } else {
                    $routeParameters[$name] = $value;
                }
            }
            $menuItemConfiguration['routeParameters'] = $routeParameters;
        }

        if ($adminName = $itemConfiguration->getParameter('admin')) {
            // retrieve an existing admin
            $admin = $this
                ->adminFactory
                ->getAdmin($adminName);

            $menuItemConfiguration['route'] = $admin->generateRouteName($itemConfiguration->getParameter('action'));
        } else if ($route = $itemConfiguration->getParameter('route')) {
            // route is provided so we take it
            $menuItemConfiguration['route'] = $route;
        } else {
            // if no admin and no route is provided, we take the url. In knp configuration, the url parameter is uri
            $menuItemConfiguration['uri'] = $itemConfiguration->getParameter('url');
        }

        return $menuItemConfiguration;
    }
}
