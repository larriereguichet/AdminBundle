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
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Contracts\EventDispatcher\EventDispatcherInterface;

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
     * @var ArrayCollection
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

    public function handleRequest(Request $request)
    {
        $this->request = $request;
        $event = new AdminEvent($this, $request);
        $this->eventDispatcher->dispatch($event, Events::ADMIN_HANDLE_REQUEST);

        if (!$event->hasAction()) {
            throw new Exception('The current action was not set during the dispatch of the event');
        }
        $this->action = $event->getAction();

        // Dispatch an event to allow entities to be filtered
        $filterEvent = new FilterEvent($this, $request);
        $this->eventDispatcher->dispatch($filterEvent, Events::ADMIN_FILTER);

        $event = new EntityEvent($this, $request);
        $event->setFilters($filterEvent->getFilters());
        $this->eventDispatcher->dispatch($event, Events::ENTITY_LOAD);

        if (null !== $event->getEntities()) {
            $this->entities = $event->getEntities();
        }
        $event = new FormEvent($this, $request);
        $this->eventDispatcher->dispatch($event, Events::ADMIN_CREATE_FORM);

        // Merge the regular forms and the filter forms
        $this->forms = array_merge($filterEvent->getForms(), $event->getForms());

        $this->handleEntityForm($request);
        $this->eventDispatcher->dispatch(new FormEvent($this, $request), Events::ADMIN_HANDLE_FORM);
    }

    public function getRequest(): Request
    {
        if (null === $this->request) {
            throw new Exception('The handleRequest() method should be called before calling getRequest()');
        }

        return $this->request;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getEntityClass(): string
    {
        return $this->configuration->get('entity');
    }

    public function getResource(): AdminResource
    {
        return $this->resource;
    }

    public function getEventDispatcher(): EventDispatcherInterface
    {
        return $this->eventDispatcher;
    }

    public function getConfiguration(): AdminConfiguration
    {
        return $this->configuration;
    }

    public function createView(): ViewInterface
    {
        if (null === $this->request) {
            throw new Exception('The handleRequest() method should be called before creating a view');
        }
        $event = new ViewEvent($this, $this->request);
        $this->eventDispatcher->dispatch($event, Events::ADMIN_VIEW);

        return $event->getView();
    }

    public function getAction(): ActionInterface
    {
        return $this->action;
    }

    public function hasAction(): bool
    {
        return null !== $this->action;
    }

    public function getEntities()
    {
        return $this->entities;
    }

    public function getForms(): array
    {
        return $this->forms;
    }

    public function hasForm(string $name): bool
    {
        return key_exists($name, $this->forms);
    }

    public function getForm(string $name): FormInterface
    {
        if (!$this->hasForm($name)) {
            throw new Exception('Form "'.$name.'" does not exists in Admin "'.$this->name.'"');
        }

        return $this->forms[$name];
    }

    private function handleEntityForm(Request $request)
    {
        if (!key_exists('entity', $this->forms)) {
            return;
        }
        $form = $this->forms['entity'];
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            dump($form->getData());
            //die;
            if ($this->entities->isEmpty()) {
                $this->entities->add($form->getData());
            }
            $event = new EntityEvent($this, $request);
            $this->eventDispatcher->dispatch($event, Events::ENTITY_SAVE);
        }
    }
}
