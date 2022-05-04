<?php

namespace LAG\AdminBundle\Event\Listener\Admin\Configuration;

use LAG\AdminBundle\Event\Events\Configuration\AdminConfigurationEvent;

class DefaultLinkedActionsListener
{
    public function __invoke(AdminConfigurationEvent $event): void
    {
//        $configuration = $event->getConfiguration();
//
//        foreach ($configuration['list_actions'] as $actionName => $actionConfiguration) {
//            if ($actionConfiguration['admin'] === null) {
//                $actionConfiguration['admin'] = $event->getAdminName();
//            }
//
//            if ($actionConfiguration['action'] === null) {
//                $actionConfiguration['action'] = $actionName;
//            }
//            $configuration['list_actions'][$actionName] = $actionConfiguration;
//        }
//        $event->setConfiguration($configuration);
    }
}
