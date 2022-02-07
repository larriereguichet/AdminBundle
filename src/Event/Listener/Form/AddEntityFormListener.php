<?php

declare(strict_types=1);

namespace LAG\AdminBundle\Event\Listener\Form;

use LAG\AdminBundle\Admin\AdminInterface;
use LAG\AdminBundle\Event\Events\FormEvent;
use LAG\AdminBundle\Form\Factory\FormFactoryInterface;

class AddEntityFormListener
{
    private FormFactoryInterface $formFactory;

    public function __construct(FormFactoryInterface $formFactory)
    {
        $this->formFactory = $formFactory;
    }

    public function __invoke(FormEvent $event): void
    {
        $admin = $event->getAdmin();
        $request = $event->getRequest();
        $action = $admin->getAction();

        if (
            $action->getName() === 'create' ||
            $action->getName() === 'edit' ||
            $action->getConfiguration()->getLoadStrategy() === AdminInterface::LOAD_STRATEGY_UNIQUE
        ) {
            $form = $this->formFactory->createEntityForm($admin, $request, $admin->getData());
            $event->addForm('entity', $form);
        }
    }
}
