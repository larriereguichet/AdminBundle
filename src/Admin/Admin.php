<?php

namespace LAG\AdminBundle\Admin;

use Doctrine\Common\Collections\ArrayCollection;
use LAG\AdminBundle\Configuration\AdminConfiguration;
use LAG\AdminBundle\Event\AdminEvent;
use LAG\AdminBundle\Event\AdminEvents;
use LAG\AdminBundle\Event\EntityEvent;
use LAG\AdminBundle\Event\FormEvent;
use LAG\AdminBundle\Event\ViewEvent;
use LAG\AdminBundle\Exception\Exception;
use LAG\AdminBundle\Resource\Resource;
use LAG\AdminBundle\View\ViewInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\HttpFoundation\Request;

class Admin implements AdminInterface
{
    /**
     * @var string
     */
    private $name;

    /**
     * @var AdminConfiguration
     */
    private $configuration;

    /**
     * @var Resource
     */
    private $resource;

    /**
     * @var EventDispatcherInterface
     */
    private $eventDispatcher;

    /**
     * @var ActionInterface
     */
    private $action;

    /**
     * @var array
     */
    private $entities;

    /**
     * Admin constructor.
     *
     * @param Resource                 $resource
     * @param AdminConfiguration       $configuration
     * @param EventDispatcherInterface $eventDispatcher
     */
    public function __construct(
        Resource $resource,
        AdminConfiguration $configuration,
        EventDispatcherInterface $eventDispatcher
    ) {
        $this->name = $resource->getName();
        $this->configuration = $configuration;
        $this->resource = $resource;
        $this->eventDispatcher = $eventDispatcher;
        $this->entities = new ArrayCollection();
    }

    /**
     * @param Request $request
     *
     * @throws Exception
     */
    public function handleRequest(Request $request)
    {
        $event = new AdminEvent($this, $request);
        $this->eventDispatcher->dispatch(AdminEvents::HANDLE_REQUEST, $event);

        if (null === $event->getAction()) {
            throw new Exception('The current action was not set during the dispatch of the event');
        }
        $this->action = $event->getAction();

        $event = new FormEvent();
        $this->eventDispatcher->dispatch(AdminEvents::HANDLE_FORM, $event);

        $event = new EntityEvent($this->configuration, $this->action->getConfiguration());
        $this->eventDispatcher->dispatch(AdminEvents::ENTITY, $event);
        $this->entities = $event->getEntities();
    }

    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @return Resource
     */
    public function getResource(): Resource
    {
        return $this->resource;
    }

    /**
     * @return EventDispatcherInterface
     */
    public function getEventDispatcher(): EventDispatcherInterface
    {
        return $this->eventDispatcher;
    }

    /**
     * @return AdminConfiguration
     */
    public function getConfiguration(): AdminConfiguration
    {
        return $this->configuration;
    }

    /**
     * @return ViewInterface
     *
     * @throws Exception
     */
    public function createView(): ViewInterface
    {
        $event = new ViewEvent($this);
        $this->eventDispatcher->dispatch(AdminEvents::VIEW, $event);

        if (null === $event->getView()) {
            throw new Exception('No event subscribers were able to create a view');
        }

        return $event->getView();
    }

    /**
     * @return ActionInterface
     *
     * @throws Exception
     */
    public function getAction(): ActionInterface
    {
        if (null === $this->action) {
            throw new Exception('The current action is not set. did you forget to call the handleRequest() method');
        }

        return $this->action;
    }

    public function getEntities()
    {
        return $this->entities;
    }
}
