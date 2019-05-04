<?php

namespace LAG\AdminBundle\Bridge\Twig\Extension;

use Doctrine\Common\Collections\Collection;
use LAG\AdminBundle\Configuration\ApplicationConfigurationStorage;
use LAG\AdminBundle\Configuration\MenuItemConfiguration;
use LAG\AdminBundle\Exception\Exception;
use LAG\AdminBundle\Factory\ConfigurationFactory;
use LAG\AdminBundle\Routing\RoutingLoader;
use LAG\AdminBundle\View\ViewInterface;
use Symfony\Component\PropertyAccess\PropertyAccess;
use Symfony\Component\Routing\RouterInterface;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

class AdminExtension extends AbstractExtension
{
    /**
     * @var RouterInterface
     */
    private $router;

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
     * @param RouterInterface                 $router
     * @param ConfigurationFactory            $configurationFactory
     */
    public function __construct(
        ApplicationConfigurationStorage $applicationConfigurationStorage,
        RouterInterface $router,
        ConfigurationFactory $configurationFactory
    ) {
        $this->router = $router;
        $this->configurationFactory = $configurationFactory;
        $this->applicationConfigurationStorage = $applicationConfigurationStorage;
    }

    public function getFunctions(): array
    {
        return [
            new TwigFunction('admin_config', [$this, 'getApplicationParameter']),
            new TwigFunction('admin_menu_action', [$this, 'getMenuAction']),
            new TwigFunction('admin_url', [$this, 'getAdminUrl']),
            new TwigFunction('admin_action_allowed', [$this, 'isAdminActionAllowed']),
        ];
    }

    public function getApplicationParameter($name)
    {
        return $this
            ->applicationConfigurationStorage
            ->getConfiguration()
            ->get($name)
        ;
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
    public function getMenuAction(MenuItemConfiguration $configuration, ViewInterface $view = null): string
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
