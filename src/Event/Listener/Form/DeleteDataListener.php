<?php

declare(strict_types=1);

namespace LAG\AdminBundle\Event\Listener\Form;

use LAG\AdminBundle\Configuration\ApplicationConfiguration;
use LAG\AdminBundle\DataPersister\Registry\DataPersisterRegistryInterface;
use LAG\AdminBundle\Event\Events\FormEvent;
use LAG\AdminBundle\Translation\Helper\TranslationHelper;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\HttpFoundation\Session\SessionInterface;

/**
 * Remove data when the remove form is submitted and valid.
 */
class DeleteDataListener
{
    /**
     * @var SessionInterface|Session
     */
    private SessionInterface $session;
    private DataPersisterRegistryInterface $registry;
    private ApplicationConfiguration $appConfig;

    public function __construct(
        DataPersisterRegistryInterface $registry,
        SessionInterface $session,
        ApplicationConfiguration $appConfig
    ) {
        $this->registry = $registry;
        $this->session = $session;
        $this->appConfig = $appConfig;
    }

    public function __invoke(FormEvent $event): void
    {
        $admin = $event->getAdmin();

        if (!$admin->hasForm('delete')) {
            return;
        }
        $form = $admin->getForm('delete');

        if ($form->isSubmitted() && $form->isValid()) {
            $dataPersister = $this->registry->get($admin->getConfiguration()->getDataPersister());
            $dataPersister->delete($admin->getData());
            $message = 'Deleted';

            if ($this->appConfig->isTranslationEnabled()) {
                $message = TranslationHelper::getTranslationKey(
                    $this->appConfig->getTranslationPattern(),
                    $admin->getName(),
                    'deleted'
                );
            }
            $this->session->getFlashBag()->add('success', $message);
        }
    }
}
