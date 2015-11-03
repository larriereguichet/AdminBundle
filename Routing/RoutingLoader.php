<?php

namespace LAG\AdminBundle\Routing;

use LAG\AdminBundle\Admin\Action;
use LAG\AdminBundle\Admin\ActionInterface;
use LAG\AdminBundle\Admin\AdminInterface;
use BlueBear\BaseBundle\Behavior\ContainerTrait;
use BlueBear\BaseBundle\Behavior\StringUtilsTrait;
use Exception;
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
    use ContainerTrait, StringUtilsTrait;

    protected $loaded = false;

    public function load($resource, $type = null)
    {
        if (true === $this->loaded) {
            throw new RuntimeException('Do not add the "extra" loader twice');
        }
        $routes = new RouteCollection();
        $admins = $this
            ->container
            ->get('lag.admin.factory')
            ->getAdmins();
        // creating a route by admin and action
        /** @var AdminInterface $admin */
        foreach ($admins as $admin) {
            $actions = $admin->getActions();
            // by default, actions are create, edit, delete, list
            /** @var Action $action */
            foreach ($actions as $action) {
                // load route into collection
                $this->loadRouteForAction($admin, $action, $routes);
                $this->loadAutomaticRoute($admin, 'batch', $routes);
            }
        }

        // loader is loaded
        $this->loaded = true;

        return $routes;
    }

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

    /**
     * Generate a route for admin and action name.
     *
     * @param $actionName
     * @param AdminInterface $admin
     *
     * @return string
     *
     * @throws Exception
     */
    public function generateRouteName($actionName, AdminInterface $admin)
    {
        if (!array_key_exists($actionName, $admin->getConfiguration()->getActions())) {
            throw new Exception("Invalid action name \"{$actionName}\" for admin \"{$admin->getName()}\" (available action are: \""
                .implode('", "', array_keys($admin->getConfiguration()->getActions())).'")');
        }
        // get routing name pattern
        $routingPattern = $admin->getConfiguration()->getRoutingNamePattern();
        // replace admin and action name in pattern
        $routeName = str_replace('{admin}', $this->underscore($admin->getName()), $routingPattern);
        $routeName = str_replace('{action}', $actionName, $routeName);

        return $routeName;
    }

    /**
     * Add a Route to the RouteCollection according to an Admin an an Action.
     *
     * @param AdminInterface  $admin
     * @param ActionInterface $action
     * @param RouteCollection $routeCollection
     *
     * @throws Exception
     */
    protected function loadRouteForAction(AdminInterface $admin, ActionInterface $action, RouteCollection $routeCollection)
    {
        $routingUrlPattern = $admin->getConfiguration()->getRoutingUrlPattern();
        // routing pattern should contains {admin} and {action}
        if (strpos($routingUrlPattern, '{admin}') == -1 || strpos($routingUrlPattern, '{action}') == -1) {
            throw new Exception('Admin routing pattern should contains {admin} and {action} placeholder');
        }
        // route path by entity name and action name
        $path = str_replace('{admin}', $this->getEntityPath($admin->getEntityNamespace()), $routingUrlPattern);
        $path = str_replace('{action}', $action->getName(), $path);
        // by default, generic controller
        $defaults = [
            '_controller' => $admin->getController().':'.$action->getName(),
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
        $routeName = $this->generateRouteName($action->getName(), $admin);
        // set route to action
        $action->getConfiguration()->setRoute($routeName);
        $action->getConfiguration()->setParameters($requirements);
        // adding route to symfony collection
        $routeCollection->add($routeName, $route);
    }

    protected function loadAutomaticRoute(AdminInterface $admin, $actionName, RouteCollection $routeCollection)
    {
        // batch route
        $path = $this->replaceInRoute($admin->getConfiguration()->getRoutingUrlPattern(), $admin->getName(), $actionName);
        $name = $this->replaceInRoute($admin->getConfiguration()->getRoutingNamePattern(), $admin->getName(), $actionName);
        $defaults = [
            '_controller' => $admin->getController().':'.$actionName,
            '_admin' => $admin->getName(),
            '_action' => $actionName,
        ];
        // adding to collection
        $routeCollection->add($name, new Route($path, $defaults));
    }

    protected function replaceInRoute($pattern, $adminName, $actionName)
    {
        $pattern = str_replace('{admin}', $adminName, $pattern);
        $pattern = str_replace('{action}', $actionName, $pattern);

        return$pattern;
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
