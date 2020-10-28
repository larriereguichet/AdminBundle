<?php

namespace LAG\AdminBundle\Event\Listener\View;

use LAG\AdminBundle\Event\Events\ViewEvent;
use LAG\AdminBundle\Factory\ViewFactoryInterface;

/**
 * Create a dynamic view using the view factory.
 */
class ViewListener
{
    private ViewFactoryInterface $viewFactory;

    public function __construct(ViewFactoryInterface $viewFactory)
    {
        $this->viewFactory = $viewFactory;
    }

    public function __invoke(ViewEvent $event): void
    {
        $admin = $event->getAdmin();
        $view = $this->viewFactory->create($event->getRequest(), $admin);
        $event->setView($view);
    }
}
