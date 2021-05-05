<?php

namespace LAG\AdminBundle\Event\Listener\Form;

use LAG\AdminBundle\DataPersister\Registry\DataPersisterRegistryInterface;
use LAG\AdminBundle\Event\Events\FormEvent;
use LAG\AdminBundle\Session\FlashMessage\FlashMessageHelperInterface;

class PersistDataListener
{
    private DataPersisterRegistryInterface $registry;
    private FlashMessageHelperInterface $flashMessageHelper;

    public function __construct(
        DataPersisterRegistryInterface $registry,
        FlashMessageHelperInterface $flashMessageHelper
    ) {
        $this->registry = $registry;
        $this->flashMessageHelper = $flashMessageHelper;
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
