<?php

namespace LAG\AdminBundle\Event\Subscriber;

use LAG\AdminBundle\Admin\Admin;
use LAG\AdminBundle\Admin\AdminInterface;
use LAG\AdminBundle\Event\AdminEvents;
use LAG\AdminBundle\Event\FormEvent;
use LAG\AdminBundle\LAGAdminBundle;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Form\FormFactoryInterface;

class FormSubscriber implements EventSubscriberInterface
{
    /**
     * @var FormFactoryInterface
     */
    private $formFactory;

    public static function getSubscribedEvents()
    {
        return [
            AdminEvents::HANDLE_FORM => 'createForm',
        ];
    }

    /**
     * FormSubscriber constructor.
     *
     * @param FormFactoryInterface $formFactory
     */
    public function __construct(FormFactoryInterface $formFactory)
    {
        $this->formFactory = $formFactory;
    }

    public function createForm(FormEvent $event)
    {
        $admin = $event->getAdmin();
        $action = $admin->getAction();
        $configuration = $action->getConfiguration();

        if (!$configuration->getParameter('use_form')) {
            return;
        }
        $entity = null;

        if (LAGAdminBundle::LOAD_STRATEGY_UNIQUE === $configuration->getParameter('load_strategy')) {
            $entity = $admin->getEntities()->first();
        }
        $form = $this
            ->formFactory
            ->create($admin->getConfiguration()->getParameter('form'), $entity)
        ;
        $event->addForm($form, 'main');
    }
}
