<?php

namespace LAG\AdminBundle\Event\Subscriber;

use LAG\AdminBundle\Event\AdminEvent;
use LAG\AdminBundle\Event\AdminEvents;
use LAG\AdminBundle\Event\MenuEvent;
use LAG\AdminBundle\Event\ViewEvent;
use LAG\AdminBundle\Exception\Exception;
use LAG\AdminBundle\Factory\ActionFactory;
use LAG\AdminBundle\Factory\ViewFactory;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class AdminSubscriber implements EventSubscriberInterface
{
    /**
     * @var ActionFactory
     */
    private $actionFactory;

    /**
     * @var ViewFactory
     */
    private $viewFactory;

    /**
     * @var EventDispatcherInterface
     */
    private $eventDispatcher;

    /**
     * @return array
     */
    public static function getSubscribedEvents()
    {
        return [
            AdminEvents::HANDLE_REQUEST => 'handleRequest',
            AdminEvents::VIEW => 'handleView',
        ];
    }

    /**
     * AdminSubscriber constructor.
     *
     * @param ActionFactory            $actionFactory
     * @param ViewFactory              $viewFactory
     * @param EventDispatcherInterface $eventDispatcher
     */
    public function __construct(
        ActionFactory $actionFactory,
        ViewFactory $viewFactory,
        EventDispatcherInterface $eventDispatcher
    ) {
        $this->actionFactory = $actionFactory;
        $this->viewFactory = $viewFactory;
        $this->eventDispatcher = $eventDispatcher;
    }

    /**
     * @param AdminEvent $event
     *
     * @throws Exception
     */
    public function handleRequest(AdminEvent $event)
    {
        $actionName = $event->getRequest()->get('_action');

        if (null === $actionName) {
            throw new Exception('The _action was not found in the request');
        }
        $admin = $event->getAdmin();
        $action = $this->actionFactory->create($actionName, $admin->getName(), $admin->getConfiguration());

        $event->setAction($action);
    }

    public function handleView(ViewEvent $event)
    {
        $admin = $event->getAdmin();
        $action = $admin->getAction();

        $menuEvent = new MenuEvent();
        $this->eventDispatcher->dispatch(AdminEvents::MENU, $menuEvent);

        $view = $this->viewFactory->create(
            $action->getName(),
            $admin->getName(),
            $admin->getConfiguration(),
            $action->getConfiguration()
        );

        $event->setView($view);
    }
}
