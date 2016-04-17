<?php

namespace LAG\AdminBundle\Menu\Factory;

use Knp\Menu\FactoryInterface;
use Knp\Menu\ItemInterface;
use LAG\AdminBundle\Admin\Behaviors\TranslationKeyTrait;
use LAG\AdminBundle\Admin\Factory\AdminFactory;
use LAG\AdminBundle\Configuration\Factory\ConfigurationFactory;
use LAG\AdminBundle\Menu\Configuration\MenuConfiguration;
use LAG\AdminBundle\Menu\Configuration\MenuItemConfiguration;
use Symfony\Component\OptionsResolver\OptionsResolver;
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
     * @return ItemInterface
     */
    public function create($name, array $configuration)
    {
        $resolver = new OptionsResolver();
        $menuConfiguration = new MenuConfiguration();
        $menuConfiguration->configureOptions($resolver);
        $menuConfiguration->setParameters($resolver->resolve($configuration));

        $menu = $this
            ->menuFactory
            ->createItem($name, [
                'childrenAttributes' => $menuConfiguration->getParameter('attr')
            ]);

        /** @var MenuItemConfiguration $itemConfiguration */
        foreach ($menuConfiguration->getParameter('items') as $itemName => $itemConfiguration) {
            $text = $this->guessItemText($itemName, $itemConfiguration);

            if ($adminName = $itemConfiguration->getParameter('admin')) {
                // retrieve an existing admin
                $admin = $this
                    ->adminFactory
                    ->getAdmin($adminName);

                $route = $admin->generateRouteName($itemConfiguration->getParameter('action'));
            } else {
                $route = $itemConfiguration->getParameter('route');
            }
            
            $menu->addChild($text, [
                'childrenAttributes' => $itemConfiguration->getParameter('attr'),
                'route' => $route,
                'routeParameters' => $itemConfiguration->getParameter('parameters'),
                'attributes' => [
                    'icon' => $itemConfiguration->getParameter('icon'),
                ]
            ]);
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
}
