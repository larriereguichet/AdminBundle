<?php

declare(strict_types=1);

namespace LAG\AdminBundle\Event\Listener\Form;

use LAG\AdminBundle\DataPersister\Registry\DataPersisterRegistryInterface;
use LAG\AdminBundle\Event\Events\FormEvent;
use LAG\AdminBundle\Session\FlashMessage\FlashMessageHelper;

class PersistDataListener
{
    public function __construct(
        private DataPersisterRegistryInterface $registry,
        private FlashMessageHelper $flashMessageHelper
    ) {
    }

    public function __invoke(FormEvent $event): void
    {
        $admin = $event->getAdmin();

        if (!$admin->hasForm('entity')) {
            return;
        }
        $form = $admin->getForm('entity');

        if ($form->isSubmitted() && $form->isValid()) {
            $configuration = $admin->getConfiguration();
            // TODO add event
            $dataPersister = $this->registry->get($configuration->getDataPersister());
            $dataPersister->save($admin->getData());
            $this->flashMessageHelper->add('success', 'lag.admin.saved');
        }
    }
}
