<?php

namespace LAG\AdminBundle\Event\Subscriber;

use LAG\AdminBundle\Admin\ActionInterface;
use LAG\AdminBundle\Admin\AdminInterface;
use LAG\AdminBundle\Event\Events;
use LAG\AdminBundle\Event\Events\FormEvent;
use LAG\AdminBundle\Factory\DataProviderFactory;
use LAG\AdminBundle\LAGAdminBundle;
use LAG\AdminBundle\Utils\StringUtils;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\Translation\TranslatorInterface;

class FormSubscriber implements EventSubscriberInterface
{
    /**
     * @var FormFactoryInterface
     */
    private $formFactory;

    /**
     * @var DataProviderFactory
     */
    private $dataProviderFactory;

    /**
     * @var Session
     */
    private $session;

    /**
     * @var TranslatorInterface
     */
    private $translator;

    /**
     * @return array
     */
    public static function getSubscribedEvents()
    {
        return [
            Events::CREATE_FORM => 'onCreateForm',
            Events::HANDLE_FORM => 'onHandleForm',
        ];
    }

    /**
     * FormSubscriber constructor.
     *
     * @param FormFactoryInterface $formFactory
     * @param DataProviderFactory  $dataProviderFactory
     * @param Session              $session
     * @param TranslatorInterface  $translator
     */
    public function __construct(
        FormFactoryInterface $formFactory,
        DataProviderFactory $dataProviderFactory,
        Session $session,
        TranslatorInterface $translator
    ) {
        $this->formFactory = $formFactory;
        $this->dataProviderFactory = $dataProviderFactory;
        $this->session = $session;
        $this->translator = $translator;
    }

    /**
     * Create a form for the loaded entity.
     *
     * @param FormEvent $event
     */
    public function onCreateForm(FormEvent $event): void
    {
        $admin = $event->getAdmin();
        $action = $admin->getAction();
        $configuration = $action->getConfiguration();

        if (!$configuration->getParameter('use_form')) {
            return;
        }
        $entity = null;

        if (LAGAdminBundle::LOAD_STRATEGY_UNIQUE === $configuration->get('load_strategy')) {
            if (!$admin->getEntities()->isEmpty()) {
                $entity = $admin->getEntities()->first();
            }
        }

        if ('create' === $action->getName()) {
            $form = $this->createEntityForm($admin, $entity);
            $event->addForm($form, 'entity');
        }

        if ('edit' === $action->getName()) {
            $form = $this->createEntityForm($admin, $entity);
            $event->addForm($form, 'entity');
        }

        if ('delete' === $action->getName()) {
            $form = $this->createDeleteForm($action, $entity);
            $event->addForm($form, 'delete');
        }
    }

    /**
     * When the HANDLE_FORM event is dispatched, we handle the form according to the current action.
     *
     * @param FormEvent $event
     */
    public function onHandleForm(FormEvent $event): void
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

    private function createEntityForm(AdminInterface $admin, $entity = null): FormInterface
    {
        if (!$entity) {
            $dataProvider = $this
                ->dataProviderFactory
                ->get($admin->getConfiguration()->get('data_provider'));
            $entity = $dataProvider->create($admin);
        }
        $form = $this
            ->formFactory
            ->create($admin->getConfiguration()->getParameter('form'), $entity)
        ;

        return $form;
    }

    private function createDeleteForm(ActionInterface $action, $entity): FormInterface
    {
        $form = $this
            ->formFactory
            ->create($action->getConfiguration()->getParameter('form'), $entity)
        ;

        return $form;
    }

    private function handleDeleteForm(Request $request, FormInterface $form, AdminInterface $admin)
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
