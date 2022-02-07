<?php

declare(strict_types=1);

namespace LAG\AdminBundle\Event\Listener\Action;

use LAG\AdminBundle\Admin\Configuration\ApplicationConfiguration;
use LAG\AdminBundle\Event\Events\Configuration\ActionConfigurationEvent;

class ConfigurationListener
{
    private ApplicationConfiguration $appConfig;

    public function __construct(ApplicationConfiguration $appConfig)
    {
        $this->appConfig = $appConfig;
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
        if (empty($configuration['route']) && !empty($configuration['admin_name'])) {
            $configuration['route'] = $this->appConfig->getRouteName(
                $configuration['admin_name'],
                $actionName
            );
        }
    }
}
