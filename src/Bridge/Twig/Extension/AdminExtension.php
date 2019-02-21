<?php

namespace LAG\AdminBundle\Bridge\Twig\Extension;

use Doctrine\Common\Collections\Collection;
use LAG\AdminBundle\Configuration\ApplicationConfigurationStorage;
use LAG\AdminBundle\Configuration\MenuItemConfiguration;
use LAG\AdminBundle\Exception\Exception;
use LAG\AdminBundle\Factory\ConfigurationFactory;
use LAG\AdminBundle\Factory\MenuFactory;
use LAG\AdminBundle\Field\EntityAwareFieldInterface;
use LAG\AdminBundle\Field\FieldInterface;
use LAG\AdminBundle\Routing\RoutingLoader;
use LAG\AdminBundle\Utils\StringUtils;
use LAG\AdminBundle\View\ViewInterface;
use Symfony\Component\PropertyAccess\PropertyAccess;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Component\Translation\TranslatorInterface;
use Twig_Environment;
use Twig_Extension;
use Twig_SimpleFunction;

class AdminExtension extends Twig_Extension
{
    /**
     * @var MenuFactory
     */
    private $menuFactory;

    /**
     * @var Twig_Environment
     */
    private $twig;

    /**
     * @var RouterInterface
     */
    private $router;

    /**
     * @var TranslatorInterface
     */
    private $translator;

    /**
     * @var ConfigurationFactory
     */
    private $configurationFactory;

    /**
     * @var ApplicationConfigurationStorage
     */
    private $applicationConfigurationStorage;

    /**
     * AdminExtension constructor.
     *
     * @param ApplicationConfigurationStorage $applicationConfigurationStorage
     * @param MenuFactory                     $menuFactory
     * @param Twig_Environment                $twig
     * @param RouterInterface                 $router
     * @param TranslatorInterface             $translator
     * @param ConfigurationFactory            $configurationFactory
     */
    public function __construct(
        ApplicationConfigurationStorage $applicationConfigurationStorage,
        MenuFactory $menuFactory,
        Twig_Environment $twig,
        RouterInterface $router,
        TranslatorInterface $translator,
        ConfigurationFactory $configurationFactory
    ) {
        $this->menuFactory = $menuFactory;
        $this->twig = $twig;
        $this->router = $router;
        $this->translator = $translator;
        $this->configurationFactory = $configurationFactory;
        $this->applicationConfigurationStorage = $applicationConfigurationStorage;
    }

    public function getFunctions()
    {
        return [
            new Twig_SimpleFunction('admin_config', [$this, 'getApplicationParameter']),
            new Twig_SimpleFunction('admin_menu', [$this, 'getMenu'], ['is_safe' => ['html']]),
            new Twig_SimpleFunction('admin_has_menu', [$this, 'hasMenu']),
            new Twig_SimpleFunction('admin_menu_action', [$this, 'getMenuAction']),
            new Twig_SimpleFunction('admin_field_header', [$this, 'getFieldHeader']),
            new Twig_SimpleFunction('admin_field', [$this, 'getField']),
            new Twig_SimpleFunction('admin_url', [$this, 'getAdminUrl']),
            new Twig_SimpleFunction('admin_action_allowed', [$this, 'isAdminActionAllowed']),
        ];
    }

    public function getApplicationParameter($name)
    {
        return $this
            ->applicationConfigurationStorage
            ->getConfiguration()
            ->getParameter($name)
        ;
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

        return $this->twig->render($menu->get('template'), [
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
    public function hasMenu(string $name)
    {
        return $this->menuFactory->hasMenu($name);
    }

    /**
     * Return the url of an menu item.
     *
     * @param MenuItemConfiguration $configuration
     * @param ViewInterface         $view
     *
     * @return string
     *
     * @throws Exception
     */
    public function getMenuAction(MenuItemConfiguration $configuration, ViewInterface $view = null)
    {
        if ($configuration->getParameter('url')) {
            return $configuration->getParameter('url');
        }
        $routeName = $configuration->getParameter('route');

        if ($configuration->getParameter('admin')) {
            $routeName = RoutingLoader::generateRouteName(
                $configuration->getParameter('admin'),
                $configuration->getParameter('action'),
                $this
                    ->applicationConfigurationStorage
                    ->getConfiguration()
                    ->getParameter('routing_name_pattern')
            );
        }
        // Map the potential parameters to the entity
        $routeParameters = [];
        $configuredParameters = $configuration->getParameter('parameters');

        if (0 !== count($configuredParameters)) {
            if (null === $view) {
                throw new Exception('A view should be provided if the menu item route requires parameters');
            }

            if (!$view->getEntities() instanceof Collection) {
                throw new Exception(
                    'Entities returned by the view should be a instance of "'.Collection::class.'" to be used in menu action'
                );
            }

            if (1 !== $view->getEntities()->count()) {
                throw new Exception('You can not map route parameters if multiple entities are loaded');
            }
            $entity = $view->getEntities()->first();
            $accessor = PropertyAccess::createPropertyAccessor();

            foreach ($configuredParameters as $name => $requirements) {
                $routeParameters[$name] = $accessor->getValue($entity, $name);
            }
        }

        return $this->router->generate($routeName, $routeParameters);
    }

    /**
     * Return the field header label.
     *
     * @param ViewInterface  $admin
     * @param FieldInterface $field
     *
     * @return string
     */
    public function getFieldHeader(ViewInterface $admin, FieldInterface $field)
    {
        if (StringUtils::startWith($field->getName(), '_')) {
            return '';
        }
        $configuration = $this->applicationConfigurationStorage->getConfiguration();

        if ($configuration->getParameter('translation')) {
            $key = StringUtils::getTranslationKey(
                $configuration->getParameter('translation_pattern'),
                $admin->getName(),
                $field->getName()
            );
            $title = $this->translator->trans($key);
        } else {
            $title = StringUtils::camelize($field->getName());
            $title = preg_replace('/(?<!\ )[A-Z]/', ' $0', $title);
            $title = trim($title);

            if ('Id' === $title) {
                $title = '#';
            }
        }

        return $title;
    }

    /**
     * Render a field of an entity.
     *
     * @param FieldInterface $field
     * @param                $entity
     *
     * @return string
     */
    public function getField(FieldInterface $field, $entity)
    {
        $value = null;
        $accessor = PropertyAccess::createPropertyAccessor();

        // if name starts with a underscore, it is a custom field, not mapped to the entity
        if ('_' !== substr($field->getName(), 0, 1)) {
            // get raw value from object
            $value = $accessor->getValue($entity, $field->getName());
        }

        if ($field instanceof EntityAwareFieldInterface) {
            $field->setEntity($entity);
        }
        $render = $field->render($value);

        return $render;
    }

    /**
     * Return the url of an Admin action.
     *
     * @param ViewInterface $view
     * @param string        $actionName
     * @param mixed|null    $entity
     *
     * @return string
     *
     * @throws Exception
     */
    public function getAdminUrl(ViewInterface $view, string $actionName, $entity = null)
    {
        if (!$this->isAdminActionAllowed($view, $actionName)) {
            throw new Exception('The action "'.$actionName.'" is not allowed for the admin "'.$view->getName().'"');
        }
        $configuration = $view->getAdminConfiguration();
        $parameters = [];
        $routeName = RoutingLoader::generateRouteName(
            $view->getName(),
            $actionName,
            $configuration->getParameter('routing_name_pattern')
        );

        if (null !== $entity) {
            $accessor = PropertyAccess::createPropertyAccessor();
            $actionConfiguration = $this->configurationFactory->createActionConfiguration(
                $actionName,
                $configuration->getParameter('actions')[$actionName],
                $view->getName(),
                $view->getAdminConfiguration()
            );

            foreach ($actionConfiguration->getParameter('route_requirements') as $name => $requirements) {
                $parameters[$name] = $accessor->getValue($entity, $name);
            }
        }

        return $this->router->generate($routeName, $parameters);
    }

    /**
     * Return true if the given action is allowed for the given Admin.
     *
     * @param ViewInterface $view
     * @param string        $actionName
     *
     * @return bool
     */
    public function isAdminActionAllowed(ViewInterface $view, string $actionName)
    {
        $configuration = $view->getAdminConfiguration();

        return key_exists($actionName, $configuration->getParameter('actions'));
    }
}
