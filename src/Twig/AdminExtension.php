<?php

namespace LAG\AdminBundle\Twig;

use LAG\AdminBundle\Configuration\ApplicationConfiguration;
use LAG\AdminBundle\Configuration\ApplicationConfigurationStorage;
use LAG\AdminBundle\Configuration\MenuItemConfiguration;
use LAG\AdminBundle\Exception\Exception;
use LAG\AdminBundle\Factory\ConfigurationFactory;
use LAG\AdminBundle\Factory\MenuFactory;
use LAG\AdminBundle\Field\EntityAwareFieldInterface;
use LAG\AdminBundle\Field\FieldInterface;
use LAG\AdminBundle\Routing\RoutingLoader;
use LAG\AdminBundle\Utils\StringUtilTrait;
use LAG\AdminBundle\View\ViewInterface;
use Symfony\Component\PropertyAccess\PropertyAccess;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Component\Translation\TranslatorInterface;
use Twig_Environment;
use Twig_Extension;
use Twig_SimpleFunction;

class AdminExtension extends Twig_Extension
{
    use StringUtilTrait;

    /**
     * @var ApplicationConfiguration
     */
    private $applicationConfiguration;

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
        $this->applicationConfiguration = $applicationConfigurationStorage->getConfiguration();
        $this->menuFactory = $menuFactory;
        $this->twig = $twig;
        $this->router = $router;
        $this->translator = $translator;
        $this->configurationFactory = $configurationFactory;
    }

    public function getFunctions()
    {
        return [
            new Twig_SimpleFunction('admin_config', [$this, 'getApplicationParameter']),
            new Twig_SimpleFunction('admin_menu', [$this, 'getMenu']),
            new Twig_SimpleFunction('admin_menu_action', [$this, 'getMenuAction']),
            new Twig_SimpleFunction('admin_field_header', [$this, 'getFieldHeader']),
            new Twig_SimpleFunction('admin_field', [$this, 'getField']),
            new Twig_SimpleFunction('admin_url', [$this, 'getAdminUrl']),
            new Twig_SimpleFunction('admin_action_allowed', [$this, 'isAdminActionAllowed']),
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

    /**
     * @param FieldInterface $field
     * @return string
     *
     * @throws Exception
     */
    public function getFieldHeader(FieldInterface $field)
    {
        if ($this->startWith($field->getName(), '_')) {
            return '';
        }

        if ($this->applicationConfiguration->getParameter('enable_translation')) {
            throw new Exception('Translation is not implemented yet');
        } else {
            $title = $this->camelize($field->getName());
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
     * @param $entity
     *
     * @return string
     */
    public function getField(FieldInterface $field, $entity)
    {
        $value = null;
        $accessor = PropertyAccess::createPropertyAccessor();

        // if name starts with a underscore, it is a custom field, not mapped to the entity
        if (substr($field->getName(), 0, 1) != '_') {
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

    public function isAdminActionAllowed(ViewInterface $view, string $actionName)
    {
        $configuration = $view->getAdminConfiguration();

        return key_exists($actionName, $configuration->getParameter('actions'));
    }
}
