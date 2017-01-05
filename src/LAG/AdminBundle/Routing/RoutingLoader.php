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
     * @param mixed $resource
     * @param null $type
     * @return RouteCollection
     * @throws Exception
     */
    public function load($resource, $type = null)
    {
        if (true === $this->loaded) {
            throw new RuntimeException('Do not add the Admin "extra" loader twice');
        }
        $routes = new RouteCollection();

        // init the AdminFactory to load the Admins
        $this
            ->adminFactory
            ->init()
        ;
        
        // get all the loaded Admins from the Registry
        $admins = $this
            ->registry
            ->all()
        ;
        
        // creating a route by admin and action
        foreach ($admins as $admin) {
            $actions = $admin
                ->getConfiguration()
                ->getParameter('actions')
            ;
    
            foreach ($actions as $name => $configuration) {
                $actionConfiguration = $this
                    ->configurationFactory
                    ->create($name, $admin->getConfiguration(), $configuration)
                ;
    
                if ($admin->getName() === 'media') {
                    dump($actionConfiguration->getParameter('route_path'));
                    dump($actionConfiguration->getParameter('route_defaults'));
                }
    
    
                // create the new route according to the resolved configuration parameters
                $route = new Route(
                    $actionConfiguration->getParameter('route_path'),
                    $actionConfiguration->getParameter('route_defaults'),
                    $actionConfiguration->getParameter('route_requirements')
                );
                $generator = new RouteNameGenerator();
                $routeName = $generator->generate($name, $admin->getName(), $admin->getConfiguration());
                
                // add the route to the collection
                $routes->add($routeName, $route);
            }
        }
        // loader is loaded
        $this->loaded = true;

        return $routes;
    }

    /**
     * @param mixed $resource
     * @param null $type
     * @return bool
     */
    public function supports($resource, $type = null)
    {
        return 'extra' === $type;
    }

    /**
     * 
     */
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
