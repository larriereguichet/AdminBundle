<?php

namespace LAG\AdminBundle\Event\Listener\Action\Configuration;

use LAG\AdminBundle\Event\Events\Configuration\ActionConfigurationEvent;
use LAG\AdminBundle\Form\Type\DeleteType;

class DefaultFormListener
{
    public function __invoke(ActionConfigurationEvent $event): void
    {
        $actionName = $event->getActionName();
        $configuration = $event->getConfiguration();


        if ($actionName === 'delete' && ($configuration['form'] ?? null) === null) {
            $configuration['form'] = DeleteType::class;
        }

        $event->setConfiguration($configuration);
    }
}
