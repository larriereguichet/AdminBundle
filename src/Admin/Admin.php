<?php

declare(strict_types=1);

namespace LAG\AdminBundle\Admin;

use LAG\AdminBundle\Configuration\AdminConfiguration;
use LAG\AdminBundle\Event\AdminEvents;
use LAG\AdminBundle\Event\Events\DataEvent;
use LAG\AdminBundle\Event\Events\FormEvent;
use LAG\AdminBundle\Event\Events\RequestEvent;
use LAG\AdminBundle\Event\Events\ViewEvent;
use LAG\AdminBundle\Exception\Exception;
use LAG\AdminBundle\View\ViewInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Contracts\EventDispatcher\EventDispatcherInterface;

class Admin implements AdminInterface
{
    private ActionInterface $action;
    private Request $request;
    private mixed $data;

    /** @var FormInterface[] */
    private array $forms = [];

    public function __construct(
        private string $name,
        private AdminConfiguration $configuration,
        private EventDispatcherInterface $eventDispatcher
    ) {
    }

    public function handleRequest(Request $request)
    {
        $this->request = $request;
        $requestEvent = new RequestEvent($this, $request);
        // The handleRequest event should be configured the current action
        $this->eventDispatcher->dispatch($requestEvent, AdminEvents::ADMIN_REQUEST);

        if (!$requestEvent->hasAction()) {
            throw new Exception('The current action was not set during the dispatch of the event');
        }
        $this->action = $requestEvent->getAction();

        // Load data from the database
        $dataEvent = new DataEvent($this, $request);
        $this->eventDispatcher->dispatch($dataEvent, AdminEvents::ADMIN_DATA);
        $this->data = $dataEvent->getData();

        // Create and handle forms
        $formEvent = new FormEvent($this, $request);

        if ($dataEvent->getFilterForm() !== null) {
            $formEvent->addForm('filter', $dataEvent->getFilterForm());
        }
        $this->eventDispatcher->dispatch($formEvent, AdminEvents::ADMIN_FORM);
        $this->forms = $formEvent->getForms();

        foreach ($this->forms as $formName => $form) {
            // The filter form has already been submitted to filter the data
            if ($formName !== 'filter') {
                $form->handleRequest($request);
            }
        }
        $this->eventDispatcher->dispatch($formEvent, AdminEvents::ADMIN_HANDLE_FORM);
    }

    public function getRequest(): Request
    {
        if (!isset($this->request)) {
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
        if (!isset($this->request)) {
            throw new Exception('The handleRequest() method should be called before creating a view');
        }
        $event = new ViewEvent($this, $this->request);
        $this->eventDispatcher->dispatch($event, AdminEvents::ADMIN_VIEW);

        return $event->getView();
    }

    public function getAction(): ActionInterface
    {
        return $this->action;
    }

    public function hasAction(): bool
    {
        return isset($this->action);
    }

    public function getForms(): array
    {
        return $this->forms;
    }

    public function hasForm(string $name): bool
    {
        return \array_key_exists($name, $this->forms);
    }

    public function getForm(string $name): FormInterface
    {
        if (!$this->hasForm($name)) {
            throw new Exception('Form "'.$name.'" does not exists in Admin "'.$this->name.'"');
        }

        return $this->forms[$name];
    }

    public function getData()
    {
        return $this->data;
    }
}
