<?php

namespace LAG\AdminBundle\Event\Subscriber;

use LAG\AdminBundle\Admin\AdminInterface;
use LAG\AdminBundle\Event\Events;
use LAG\AdminBundle\Event\Events\FormEvent;
use LAG\AdminBundle\Factory\DataProviderFactory;
use LAG\AdminBundle\Factory\FormFactoryInterface;
use LAG\AdminBundle\LAGAdminBundle;
use LAG\AdminBundle\Utils\StringUtils;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

class FormSubscriber implements EventSubscriberInterface
{
    /**
     * @var DataProviderFactory
     */
    private $dataProviderFactory;

    /**
     * @var Session|SessionInterface
     */
    private $session;

    /**
     * @var TranslatorInterface
     */
    private $translator;

    /**
     * @var FormFactoryInterface
     */
    private $adminFormFactory;

    /**
     * @return array
     */
    public static function getSubscribedEvents()
    {
        return [
            Events::CREATE_FORM => 'createForm',
            Events::HANDLE_FORM => 'handleForm',
        ];
    }

    /**
     * FormSubscriber constructor.
     *
     * @param DataProviderFactory  $dataProviderFactory
     * @param FormFactoryInterface $adminFormFactory
     * @param SessionInterface     $session
     * @param TranslatorInterface  $translator
     */
    public function __construct(
        DataProviderFactory $dataProviderFactory,
        FormFactoryInterface $adminFormFactory,
        SessionInterface $session,
        TranslatorInterface $translator
    ) {
        $this->dataProviderFactory = $dataProviderFactory;
        $this->session = $session;
        $this->translator = $translator;
        $this->adminFormFactory = $adminFormFactory;
    }

    /**
     * Create a form for the loaded entity.
     *
     * @param FormEvent $event
     */
    public function createForm(FormEvent $event): void
    {
        $admin = $event->getAdmin();
        $action = $admin->getAction();
        $configuration = $action->getConfiguration();

        if (!$configuration->get('use_form')) {
            return;
        }
        $entity = null;

        if (LAGAdminBundle::LOAD_STRATEGY_UNIQUE === $configuration->get('load_strategy')) {
            if (!$admin->getEntities()->isEmpty()) {
                $entity = $admin->getEntities()->first();
            }
        }

        if ('create' === $action->getName() || 'edit' === $action->getName()) {
            $form = $this->adminFormFactory->createEntityForm($admin, $entity);
            $event->addForm($form, 'entity');
        }

        if ('delete' === $action->getName()) {
            $form = $this->adminFormFactory->createDeleteForm($action, $entity);
            $event->addForm($form, 'delete');
        }
    }

    /**
     * When the HANDLE_FORM event is dispatched, we handle the form according to the current action.
     *
     * @param FormEvent $event
     */
    public function handleForm(FormEvent $event): void
    {
        $admin = $event->getAdmin();
        $action = $admin->getAction();

        if ('delete' === $action->getName()) {
            if (!$admin->hasForm('delete')) {
                return;
            }
            $form = $admin->getForm('delete');
            $this->handleDeleteForm($event->getRequest(), $form, $admin);
        }
    }

    private function handleDeleteForm(Request $request, FormInterface $form, AdminInterface $admin): void
    {
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $dataProvider = $this
                ->dataProviderFactory
                ->get($admin->getConfiguration()->get('data_provider'))
            ;
            $dataProvider->delete($admin);

            $message = StringUtils::getTranslationKey(
                $admin->getConfiguration()->get('translation_pattern'),
                $admin->getName(),
                'delete_success'
            );
            $this->session->getFlashBag()->add('success', $this->translator->trans($message));
        }
    }
}
