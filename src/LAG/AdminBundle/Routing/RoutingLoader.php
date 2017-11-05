<?php

namespace LAG\AdminBundle\Routing;

use Exception;
use LAG\AdminBundle\Admin\Factory\AdminFactory;
use LAG\AdminBundle\Admin\Registry\Registry;
use LAG\AdminBundle\Action\Factory\ConfigurationFactory;
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
     * @var AdminFactory
     */
    private $adminFactory;
    
    /**
     * @var Registry
     */
    private $registry;
    
    /**
     * @var ConfigurationFactory
     */
    private $configurationFactory;
    
    /**
     * RoutingLoader constructor.
     *
     * @param AdminFactory $adminFactory
     * @param Registry $registry
     * @param ConfigurationFactory $configurationFactory
     */
    public function __construct(AdminFactory $adminFactory, Registry $registry, ConfigurationFactory $configurationFactory)
    {
        $this->adminFactory = $adminFactory;
        $this->registry = $registry;
        $this->configurationFactory = $configurationFactory;
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

        // Load the Admins
        $this
            ->adminFactory
            ->init()
        ;
        $admins = $this
            ->registry
            ->all()
        ;
        
        // Creating a route by Admin and Action
        foreach ($admins as $admin) {
            $actions = $admin
                ->getConfiguration()
                ->getParameter('actions')
            ;
    
            foreach ($actions as $name => $configuration) {
                $actionConfiguration = $this
                    ->configurationFactory
                    ->create($name, $admin->getName(), $admin->getConfiguration(), $configuration)
                ;
                // Create the new route according to the resolved configuration parameters
                $route = new Route(
                    $actionConfiguration->getParameter('route_path'),
                    $actionConfiguration->getParameter('route_defaults'),
                    $actionConfiguration->getParameter('route_requirements')
                );
                $generator = new RouteNameGenerator();
                $routeName = $generator->generate($name, $admin->getName(), $admin->getConfiguration());
                
                // Add the route to the collection
                $routes->add($routeName, $route);
            }
        }
        $this->loaded = true;

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
