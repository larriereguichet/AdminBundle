<?php

namespace BlueBear\AdminBundle\Routing;

use BlueBear\AdminBundle\Admin\Action;
use BlueBear\AdminBundle\Admin\Admin;
use BlueBear\BaseBundle\Behavior\ContainerTrait;
use Exception;
use RuntimeException;
use Symfony\Component\Config\Loader\LoaderInterface;
use Symfony\Component\Config\Loader\LoaderResolverInterface;
use Symfony\Component\Routing\Route;
use Symfony\Component\Routing\RouteCollection;

/**
 * RoutingLoader
 *
 * Creates routing for configured entities
 */
class RoutingLoader implements LoaderInterface
{
    use ContainerTrait;

    protected $loaded = false;

    public function load($resource, $type = null)
    {
        if (true === $this->loaded) {
            throw new RuntimeException('Do not add the "extra" loader twice');
        }
        $routes = new RouteCollection();
        $admins = $this->getContainer()->get('bluebear.admin.factory')->getAdmins();
        // creating a route by admin and action
        /** @var Admin $admin */
        foreach ($admins as $admin) {
            $actions = $admin->getActions();
            // by default, actions are create, edit, delete, list
            /** @var Action $action */
            foreach ($actions as $action) {
                // load route into collection
                $this->loadRouteForAction($admin, $action, $routes);
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

    protected function loadRouteForAction(Admin $admin, Action $action, RouteCollection $routeCollection)
    {
        $routingUrlPattern = $admin->getConfiguration()->routingUrlPattern;
        // routing pattern should contains {admin} and {action}
        if (strpos($routingUrlPattern, '{admin}') == -1 || strpos($routingUrlPattern, '{action}') == -1) {
            throw new Exception('Admin routing pattern should contains {admin} and {action} placeholder');
        }
        // route path by entity name and action name
        $path = str_replace('{admin}', $admin->getEntityPath(), $routingUrlPattern);
        $path = str_replace('{action}', $action->getName(), $path);
        // by default, generic controller
        $defaults = [
            '_controller' => $admin->getController() . ':' . $action->getName(),
            '_admin' => $admin->getName(),
            '_action' => $action->getName()
        ];
        // by default, no requirements
        $requirements = [];
        // for delete and edit action, an id is required
        if (in_array($action, ['delete', 'edit'])) {
            $path .= '/{id}';
            $requirements = [
                'id' => '\d+'
            ];
        }
        // creating new route
        $route = new Route($path, $defaults, $requirements);
        $routeName = $admin->generateRouteName($action->getName());
        // set route to action
        $action->setRoute($routeName);
        // adding route to symfony collection
        $routeCollection->add($routeName, $route);
    }
}
