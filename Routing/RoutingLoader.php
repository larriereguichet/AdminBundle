<?php

namespace LAG\AdminBundle\Routing;

use LAG\AdminBundle\Action\ActionInterface;
use LAG\AdminBundle\Admin\AdminInterface;
use Exception;
use LAG\AdminBundle\Admin\Factory\AdminFactory;
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
    protected $loaded = false;

    /**
     * @var AdminFactory
     */
    protected $adminFactory;

    /**
     * RoutingLoader constructor.
     *
     * @param AdminFactory $adminFactory
     */
    public function __construct(AdminFactory $adminFactory)
    {
        $this->adminFactory = $adminFactory;
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
            throw new RuntimeException('Do not add the "extra" loader twice');
        }
        $routes = new RouteCollection();

        // init the AdminFactory to load the Admins
        $this
            ->adminFactory
            ->init();

        // get all the loaded Admins from the Registry
        $admins = $this
            ->adminFactory
            ->getRegistry()
            ->all();
        
        // creating a route by admin and action
        foreach ($admins as $admin) {
            $actions = $admin->getActions();
            
            // by default, the actions are create, edit, delete, list
            foreach ($actions as $action) {
                // load the route for the current action into the route collection
                $this->loadRouteForAction($admin, $action, $routes);
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

    /**
     * Add a Route to the RouteCollection according to an Admin an an Action.
     *
     * @param AdminInterface $admin
     * @param ActionInterface $action
     * @param RouteCollection $routeCollection
     *
     * @throws Exception
     */
    protected function loadRouteForAction(AdminInterface $admin, ActionInterface $action, RouteCollection $routeCollection)
    {
        $routingUrlPattern = $admin
            ->getConfiguration()
            ->getParameter('routing_url_pattern');

        // routing pattern should contains {admin} and {action}
        if (strpos($routingUrlPattern, '{admin}') == -1 || strpos($routingUrlPattern, '{action}') == -1) {
            throw new Exception('Admin routing pattern should contains {admin} and {action} placeholder');
        }
        // route path by entity name and action name
        $path = str_replace('{admin}', $admin->getName(), $routingUrlPattern);
        $path = str_replace('{action}', $action->getName(), $path);

        // by default, generic controller
        $defaults = [
            '_controller' => $admin->getConfiguration()->getParameter('controller').':'.$action->getName(),
            '_admin' => $admin->getName(),
            '_action' => $action->getName(),
        ];
        // by default, no requirements
        $requirements = [];

        // for delete and edit action, an id is required
        if (in_array($action->getName(), ['delete', 'edit'])) {
            $path .= '/{id}';
            $requirements = [
                'id' => '\d+',
            ];
        }
        // creating new route
        $route = new Route($path, $defaults, $requirements);
        $routeName = $admin->generateRouteName($action->getName());

        // replace action route configuration
        $actionConfiguration = $action
            ->getConfiguration()
            ->getParameters();

        $actionConfiguration['route'] = $routeName;
        $actionConfiguration['parameters'] = $requirements;

        $action
            ->getConfiguration()
            ->setParameters($actionConfiguration);

        // adding route to symfony collection
        $routeCollection->add($routeName, $route);
    }

    /**
     * @param $pattern
     * @param $adminName
     * @param $actionName
     * @return mixed
     */
    protected function replaceInRoute($pattern, $adminName, $actionName)
    {
        $pattern = str_replace('{admin}', $adminName, $pattern);
        $pattern = str_replace('{action}', $actionName, $pattern);

        return $pattern;
    }

    /**
     * Return entity path for routing (for example, MyNamespace\EntityName => entityName).
     *
     * @param $namespace
     *
     * @return string
     */
    protected function getEntityPath($namespace)
    {
        $array = explode('\\', $namespace);
        $path = array_pop($array);
        $path = strtolower(substr($path, 0, 1)).substr($path, 1);

        return $path;
    }
}
