<?php

namespace LAG\AdminBundle\Event\Listener\Action;

use LAG\AdminBundle\Configuration\ApplicationConfiguration;
use LAG\AdminBundle\Event\Events\Configuration\ActionConfigurationEvent;

class ActionConfigurationListener
{
    private ApplicationConfiguration $appConfig;

    public function __construct(ApplicationConfiguration $appConfig)
    {
        $this->appConfig = $appConfig;
    }

    public function __invoke(ActionConfigurationEvent $event): void
    {
        $configuration = $event->getConfiguration();

        if (empty($configuration['route'])) {
            $configuration['route'] = $this->appConfig->getRouteName(
                $configuration['admin_name'],
                $event->getActionName()
            );
        }
        $event->setConfiguration($configuration);
    }
}
