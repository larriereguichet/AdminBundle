<?php

namespace LAG\AdminBundle\Factory\Configuration;

use Exception;
use LAG\AdminBundle\Admin\Helper\AdminHelperInterface;
use LAG\AdminBundle\Configuration\ApplicationConfiguration;
use LAG\AdminBundle\Configuration\MenuConfiguration;
use LAG\AdminBundle\Event\Events\Configuration\MenuConfigurationEvent;
use LAG\AdminBundle\Event\MenuEvents;
use LAG\AdminBundle\Exception\ConfigurationException;
use Symfony\Component\PropertyAccess\PropertyAccessor;
use Symfony\Contracts\EventDispatcher\EventDispatcherInterface;

class MenuConfigurationFactory implements MenuConfigurationFactoryInterface
{
    private EventDispatcherInterface $eventDispatcher;
    private AdminHelperInterface $adminHelper;
    private ApplicationConfiguration $appConfig;

    public function __construct(
        EventDispatcherInterface $eventDispatcher,
        AdminHelperInterface $adminHelper,
        ApplicationConfiguration $appConfig
    ) {
        $this->eventDispatcher = $eventDispatcher;
        $this->adminHelper = $adminHelper;
        $this->appConfig = $appConfig;
    }

    public function create(string $menuName, array $options = []): MenuConfiguration
    {
        $event = new MenuConfigurationEvent($menuName, $options);
        $this->eventDispatcher->dispatch($event, MenuEvents::MENU_CONFIGURATION);
        $options = $event->getMenuConfiguration();
        $menuConfiguration = new MenuConfiguration($menuName);

        try {
            $options = $this->configureAdminRoutes($options);
            $menuConfiguration->configure($options);
        } catch (Exception $exception) {
            throw new ConfigurationException('menu', $menuName, $exception);
        }

        return $menuConfiguration;
    }

    private function configureAdminRoutes(array $options): array
    {
        if (empty($options['children']) || !is_array($options)) {
            return $options;
        }

        foreach ($options['children'] as $name => $child) {
            if (empty($child['admin']) || empty($child['action'])) {
                continue;
            }
            $options['children'][$name]['route'] = $this->appConfig->getRouteName($child['admin'], $child['action']);

            if (empty($child['routeParameters'])) {
                continue;
            }
            $options['children'][$name]['routeParameters'] = $this->configureAdminRouteParameters($child['routeParameters']);
        }

        return $options;
    }

    private function configureAdminRouteParameters(array $routeParameters): array
    {
        $accessor = new PropertyAccessor(true);
        $data = $this->adminHelper->getAdmin()->getData();

        if (!is_object($data) || is_array($data)) {
            throw new \LAG\AdminBundle\Exception\Exception('The data should be an object or an array to generate a menu with dynamic values');
        }

        foreach ($routeParameters as $name => $value) {
            // Dashed value are static
            $hasDoubleDash = '__' === substr($value, 0, 2);

            if (null === $value) {
                $value = $name;
            }

            if ($data && $accessor->isReadable($data, $value) && !$hasDoubleDash) {
                $routeParameters[$name] = $accessor->getValue($data, $value);
            } elseif ($hasDoubleDash) {
                $routeParameters[$name] = substr($value, 2);
            } else {
                $routeParameters[$name] = $value;
            }
        }

        return $routeParameters;
    }
}
