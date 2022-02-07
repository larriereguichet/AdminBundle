<?php

declare(strict_types=1);

namespace LAG\AdminBundle\Event\Listener\View;

use LAG\AdminBundle\Event\Events\ViewEvent;
use LAG\AdminBundle\View\Factory\ViewFactoryInterface;

/**
 * Create a dynamic view using the factory.
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
        $view = $this->viewFactory->create($event->getRequest(), $event->getAdmin());
        $event->setView($view);
    }
}
