<?php

declare(strict_types=1);

namespace LAG\AdminBundle\Event\Listener\Form;

use LAG\AdminBundle\Event\Events\FormEvent;
use LAG\AdminBundle\Factory\AdminFormFactoryInterface;

class CreateDeleteFormListener
{
    private AdminFormFactoryInterface $formFactory;

    public function __construct(AdminFormFactoryInterface $formFactory)
    {
        $this->formFactory = $formFactory;
    }

    public function __invoke(FormEvent $event): void
    {
        $admin = $event->getAdmin();
        $request = $event->getRequest();
        $action = $admin->getAction();

        if ($action->getName() === 'delete') {
            $form = $this->formFactory->createDeleteForm($admin, $request, $admin->getData());
            $event->addForm('delete', $form);
        }
    }
}
