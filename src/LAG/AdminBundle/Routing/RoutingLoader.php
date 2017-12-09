<?php

namespace LAG\AdminBundle\Routing;

use Exception;
use LAG\AdminBundle\Action\Factory\ConfigurationFactory as ActionConfigurationFactory;
use LAG\AdminBundle\Admin\Factory\ConfigurationFactory;
use RuntimeException;
use Symfony\Component\Config\Loader\LoaderInterface;
use Symfony\Component\Config\Loader\LoaderResolverInterface;
use Symfony\Component\Routing\Route;
use Symfony\Component\Routing\RouteCollection;

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
     * @var array
     */
    private $adminConfigurations;

    /**
     * @var ConfigurationFactory
     */
    private $adminConfigurationFactory;

    /**
     * @var ActionConfigurationFactory
     */
    private $actionConfigurationFactory;

    /**
     * RoutingLoader constructor.
     *
     * @param array                      $adminConfigurations
     * @param ConfigurationFactory       $adminConfigurationFactory
     * @param ActionConfigurationFactory $actionConfigurationFactory
     */
    public function __construct(
        array $adminConfigurations = [],
        ConfigurationFactory $adminConfigurationFactory,
        ActionConfigurationFactory $actionConfigurationFactory
    ) {
        $this->adminConfigurations = $adminConfigurations;
        $this->adminConfigurationFactory = $adminConfigurationFactory;
        $this->actionConfigurationFactory = $actionConfigurationFactory;
    }

    /**
     * Load the Admin's route.
     *
     * @param mixed  $resource
     * @param string $type
     *
     * @return RouteCollection
     *
     * @throws Exception
     */
    public function load($resource, $type = null)
    {
        if (true === $this->loaded) {
            throw new RuntimeException('Do not add the Admin "extra" loader twice');
        }
        $routes = new RouteCollection();

        // Creating a route by Admin and Action
        foreach ($this->adminConfigurations as $adminName => $adminConfiguration) {
            $adminConfiguration = $this
                ->adminConfigurationFactory
                ->create($adminConfiguration)
            ;

            foreach ($adminConfiguration->getParameter('actions') as $actionName => $actionConfiguration) {
                $actionConfiguration = $this
                    ->actionConfigurationFactory
                    ->create($actionName, $adminName, $adminConfiguration, $actionConfiguration)
                ;
                // Create the new route according to the resolved configuration parameters
                $route = new Route(
                    $actionConfiguration->getParameter('route_path'),
                    $actionConfiguration->getParameter('route_defaults'),
                    $actionConfiguration->getParameter('route_requirements')
                );
                $generator = new RouteNameGenerator();
                $routeName = $generator->generate($actionName, $adminName, $adminConfiguration);

                // Add the route to the collection
                $routes->add($routeName, $route);
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

    /**
     * @param LoaderResolverInterface $resolver
     */
    public function setResolver(LoaderResolverInterface $resolver)
    {
    }
}
