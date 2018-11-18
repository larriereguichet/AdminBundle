<?php

namespace LAG\AdminBundle\Admin;

use Doctrine\Common\Collections\ArrayCollection;
use LAG\AdminBundle\Configuration\AdminConfiguration;
use LAG\AdminBundle\Event\Events;
use LAG\AdminBundle\Event\Events\AdminEvent;
use LAG\AdminBundle\Event\Events\EntityEvent;
use LAG\AdminBundle\Event\Events\FilterEvent;
use LAG\AdminBundle\Event\Events\FormEvent;
use LAG\AdminBundle\Event\Events\ViewEvent;
use LAG\AdminBundle\Exception\Exception;
use LAG\AdminBundle\Resource\AdminResource;
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
     * @var AdminResource
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
     * @param AdminResource            $resource
     * @param AdminConfiguration       $configuration
     * @param EventDispatcherInterface $eventDispatcher
     */
    public function __construct(
        AdminResource $resource,
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
     * @inheritdoc
     */
    public function handleRequest(Request $request)
    {
        $this->request = $request;
        $event = new AdminEvent($this, $request);
        $this->eventDispatcher->dispatch(Events::HANDLE_REQUEST, $event);

        if (!$event->hasAction()) {
            throw new Exception('The current action was not set during the dispatch of the event');
        }
        $this->action = $event->getAction();

        // Dispatch an event to allow entities to be filtered
        $filterEvent = new FilterEvent($this, $request);
        $this->eventDispatcher->dispatch(Events::FILTER, $filterEvent);

        $event = new EntityEvent($this, $request);
        $event->setFilters($filterEvent->getFilters());
        $this->eventDispatcher->dispatch(Events::ENTITY_LOAD, $event);

        if (null !== $event->getEntities()) {
            $this->entities = $event->getEntities();
        }

        $event = new FormEvent($this, $request);
        $this->eventDispatcher->dispatch(Events::CREATE_FORM, $event);

        // Merge the regular forms and the filter forms
        $this->forms = array_merge($event->getForms(), $filterEvent->getForms());

        $this->handleEntityForm($request);
        $this->eventDispatcher->dispatch(Events::HANDLE_FORM, new FormEvent($this, $request));
    }

    /**
     * @inheritdoc
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @inheritdoc
     */
    public function getResource(): AdminResource
    {
        return $this->resource;
    }

    /**
     * @inheritdoc
     */
    public function getEventDispatcher(): EventDispatcherInterface
    {
        return $this->eventDispatcher;
    }

    /**
     * @inheritdoc
     */
    public function getConfiguration(): AdminConfiguration
    {
        return $this->configuration;
    }

    /**
     * @inheritdoc
     */
    public function createView(): ViewInterface
    {
        $event = new ViewEvent($this, $this->request);
        $this->eventDispatcher->dispatch(Events::VIEW, $event);

        return $event->getView();
    }

    /**
     * @inheritdoc
     */
    public function getAction(): ActionInterface
    {
        return $this->action;
    }

    /**
     * @inheritdoc
     */
    public function hasAction(): bool
    {
        return null !== $this->action;
    }

    /**
     * @inheritdoc
     */
    public function getEntities()
    {
        return $this->entities;
    }

    /**
     * @inheritdoc
     */
    public function getForms(): array
    {
        return $this->forms;
    }

    /**
     * @inheritdoc
     */
    public function hasForm(string $name): bool
    {
        return key_exists($name, $this->forms);
    }

    /**
     * @inheritdoc
     */
    public function getForm(string $name): FormInterface
    {
        if (!$this->hasForm($name)) {
            throw new Exception('Form "'.$name.'" does not exists in Admin "'.$this->name.'"');
        }

        return $this->forms[$name];
    }

    /**
     * @inheritdoc
     */
    private function handleEntityForm(Request $request)
    {
        if (!key_exists('entity', $this->forms)) {
            return;
        }
        $form = $this->forms['entity'];
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            if ($this->entities->isEmpty()) {
                $this->entities->add($form->getData());
            }
            $event = new EntityEvent($this, $request);
            $this->eventDispatcher->dispatch(Events::ENTITY_SAVE, $event);
        }
    }
}
