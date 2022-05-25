<?php

declare(strict_types=1);

namespace LAG\AdminBundle\Event\Listener\Admin\Configuration;

use LAG\AdminBundle\Application\Configuration\ApplicationConfiguration;
use LAG\AdminBundle\Event\Events\Configuration\AdminConfigurationEvent;

class DefaultRouteListener
{
    private ApplicationConfiguration $applicationConfiguration;

    public function __construct(ApplicationConfiguration $applicationConfiguration)
    {
        $this->applicationConfiguration = $applicationConfiguration;
    }

    public function __invoke(AdminConfigurationEvent $event): void
    {
        $configuration = $event->getConfiguration();

        foreach ($configuration['item_actions'] ?? [] as $actionName => $actionConfiguration) {
            $configuration['item_actions'][$actionName] = $this->configureDefaultRoute($actionName, $actionConfiguration);
        }

        foreach ($configuration[index_actions] ?? [] as $actionName => $actionConfiguration) {
            $configuration[index_actions][$actionName] = $this->configureDefaultRoute($actionName, $actionConfiguration);
        }

        $event->setConfiguration($configuration);
    }

    private function configureDefaultRoute(string $actionName, array $configuration): array
    {
        if (empty($configuration['route'])) {
            return $configuration;
        }

        if (!empty($configuration['admin_name'])) {
            return $configuration;
        }

        if (!empty($configuration['action_name'])) {
            return $configuration;
        }
        $configuration['route'] = $this->applicationConfiguration->getRouteName(
            $configuration['admin_name'],
            $actionName
        );

        return $configuration;
    }
}
