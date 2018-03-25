<?php

namespace LAG\AdminBundle\Admin;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use LAG\AdminBundle\Configuration\AdminConfiguration;
use LAG\AdminBundle\Event\AdminEvent;
use LAG\AdminBundle\Event\AdminEvents;
use LAG\AdminBundle\Event\EntityEvent;
use LAG\AdminBundle\Event\FilterEvent;
use LAG\AdminBundle\Event\FormEvent;
use LAG\AdminBundle\Event\ViewEvent;
use LAG\AdminBundle\Exception\Exception;
use LAG\AdminBundle\Resource\Resource;
use LAG\AdminBundle\View\ViewInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\Form\FormInterface;
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
     * @var FormInterface[]
     */
    private $forms = [];

    /**
     * @var Request
     */
    private $request;

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
        $this->request = $request;
        $event = new AdminEvent($this, $request);
        $this->eventDispatcher->dispatch(AdminEvents::HANDLE_REQUEST, $event);

        if (!$event->hasAction()) {
            throw new Exception('The current action was not set during the dispatch of the event');
        }
        $this->action = $event->getAction();

        // Dispatch an event to allow entities to be filtered
        $filterEvent = new FilterEvent($this, $request);
        $this->eventDispatcher->dispatch(AdminEvents::FILTER, $filterEvent);

        $event = new EntityEvent($this, $request);
        $event->setFilters($filterEvent->getFilters());
        $this->eventDispatcher->dispatch(AdminEvents::ENTITY_LOAD, $event);
        $this->entities = $event->getEntities();

        $event = new FormEvent($this, $request);
        $this->eventDispatcher->dispatch(AdminEvents::HANDLE_FORM, $event);

        // Merge the regular forms and the filter forms
        $this->forms = array_merge($event->getForms(), $filterEvent->getForms());

        $this->handleForm($request);
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
     */
    public function createView(): ViewInterface
    {
        $event = new ViewEvent($this, $this->request);
        $this->eventDispatcher->dispatch(AdminEvents::VIEW, $event);

        return $event->getView();
    }

    /**
     * @return ActionInterface
     */
    public function getAction(): ActionInterface
    {
        return $this->action;
    }

    /**
     * @return bool
     */
    public function hasAction(): bool
    {
        return null !== $this->action;
    }

    /**
     * Return the entities loaded from the data provider.
     *
     * @return Collection
     */
    public function getEntities()
    {
        return $this->entities;
    }

    /**
     * @return FormInterface[]
     */
    public function getForms(): array
    {
        return $this->forms;
    }

    /**
     * Submit a form linked to the Admin's entity if required.
     *
     * @param Request $request
     */
    private function handleForm(Request $request)
    {
        if (!key_exists('entity', $this->forms)) {
            return;
        }
        $form = $this->forms['entity'];
        $entity = $this->entities->first();

        if (!$entity) {
            return;
        }
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $event = new EntityEvent($this, $request);
            $this->eventDispatcher->dispatch(AdminEvents::ENTITY_SAVE, $event);
        }
    }
}
