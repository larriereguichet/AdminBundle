<?php

namespace LAG\AdminBundle\Routing;

use Exception;
use LAG\AdminBundle\Configuration\ApplicationConfiguration;
use LAG\AdminBundle\Configuration\ApplicationConfigurationStorage;
use LAG\AdminBundle\Controller\HomeAction;
use LAG\AdminBundle\Factory\ConfigurationFactory;
use LAG\AdminBundle\Resource\Registry\ResourceRegistryInterface;
use RuntimeException;
use Symfony\Component\Config\Loader\LoaderInterface;
use Symfony\Component\Config\Loader\LoaderResolverInterface;
use Symfony\Component\Routing\Route;
use Symfony\Component\Routing\RouteCollection;
use Symfony\Contracts\EventDispatcher\EventDispatcherInterface;

/**
 * RoutingLoader.
 *
 * Creates routing for configured entities
 */
class RoutingLoader implements LoaderInterface
{
    /**
     * @var bool
     */
    private $loaded = false;

    /**
     * @var EventDispatcherInterface
     */
    private $eventDispatcher;

    /**
     * @var ApplicationConfiguration
     */
    private $applicationConfiguration;

    /**
     * @var ConfigurationFactory
     */
    private $configurationFactory;

    /**
     * @var ResourceRegistryInterface
     */
    private $registry;

    /**
     * RoutingLoader constructor.
     */
    public function __construct(
        ResourceRegistryInterface $registry,
        EventDispatcherInterface $eventDispatcher,
        ApplicationConfigurationStorage $applicationConfigurationStorage,
        ConfigurationFactory $configurationFactory
    ) {
        $this->eventDispatcher = $eventDispatcher;
        $this->applicationConfiguration = $applicationConfigurationStorage->getConfiguration();
        $this->configurationFactory = $configurationFactory;
        $this->registry = $registry;
    }

    /**
     * @return string
     */
    public static function generateRouteName(string $adminName, string $actionName, string $routingPattern)
    {
        // generate the route name using the configured pattern
        $routeName = str_replace(
            '{admin}',
            strtolower($adminName),
            $routingPattern
        );
        $routeName = str_replace(
            '{action}',
            $actionName,
            $routeName
        );

        return $routeName;
    }

    /**
     * Load the Admin's route.
     *
     * @param mixed  $routingResource
     * @param string $type
     *
     * @return RouteCollection
     *
     * @throws Exception
     */
    public function load($routingResource, $type = null)
    {
        if ($this->loaded) {
            throw new RuntimeException('Do not add the Admin "extra" loader twice');
        }
        $routes = new RouteCollection();

        foreach ($this->registry->all() as $name => $resource) {
            $configuration = $this
                ->configurationFactory
                ->createAdminConfiguration(
                    $resource->getName(),
                    $resource->getConfiguration(),
                    $this->applicationConfiguration
                )
            ;

            foreach ($configuration->getParameter('actions') as $actionName => $actionData) {
                $actionConfiguration = $this
                    ->configurationFactory
                    ->createActionConfiguration($actionName, $actionData, $name, $configuration)
                ;
                $route = new Route(
                    $actionConfiguration->getParameter('route_path'),
                    $actionConfiguration->getParameter('route_defaults'),
                    $actionConfiguration->getParameter('route_requirements')
                );
                $routes->add($actionConfiguration->getParameter('route'), $route);
            }

            if ($this->applicationConfiguration->getParameter('enable_homepage')) {
                $route = new Route('/', ['_controller' => HomeAction::class], []);
                $routes->add('lag.admin.homepage', $route);
            }
        }

        return $routes;
    }

    /**
     * Return true for the extra resource.
     *
     * @param mixed $resource
     * @param string $type
     *
     * @return bool
     */
    public function supports($resource, $type = null)
    {
        return 'extra' === $type;
    }

    public function getResolver()
    {
    }

    public function setResolver(LoaderResolverInterface $resolver)
    {
    }
}
