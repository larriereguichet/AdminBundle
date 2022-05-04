<?php

declare(strict_types=1);

namespace LAG\AdminBundle\Event\Listener\Action\Configuration;

use LAG\AdminBundle\Application\Configuration\ApplicationConfiguration;
use LAG\AdminBundle\Event\Events\Configuration\ActionConfigurationEvent;

class DefaultRouteListener
{
    private ApplicationConfiguration $applicationConfiguration;

    public function __construct(ApplicationConfiguration $applicationConfiguration)
    {
        $this->applicationConfiguration = $applicationConfiguration;
    }

    public function __invoke(ActionConfigurationEvent $event): void
    {
        $actionName = $event->getActionName();
        $configuration = $event->getConfiguration();

        $this->configureDefaultRoute($actionName, $configuration);
        $event->setConfiguration($configuration);
    }

    private function configureDefaultRoute(string $actionName, array &$configuration): void
    {
        if (!empty($configuration['route'])) {
            return;
        }
        $configuration['route'] = $this->applicationConfiguration->getRouteName(
            $configuration['admin_name'],
            $actionName
        );
    }
}
