<?php

declare(strict_types=1);

namespace LAG\AdminBundle\Admin;

use LAG\AdminBundle\Action\Show;
use LAG\AdminBundle\Bridge\Doctrine\ORM\ORMDataProcessor;
use LAG\AdminBundle\Bridge\Doctrine\ORM\ORMDataProvider;
use LAG\AdminBundle\Controller\Update;
use LAG\AdminBundle\Metadata\Create;
use LAG\AdminBundle\Metadata\Delete;
use LAG\AdminBundle\Metadata\Index;

/** @deprecated  */
class Admin implements AdminInterface
{
    public function __construct(
        private string $name,
        private string $dataClass,
        private ?string $title = null,
        private ?string $group = null,
        private ?string $icon = null,
        private ?string $adminClass = Admin::class,
        private array $actions = [
            new Index(),
            new Create(),
            new Update(),
            new Delete(),
            new Show(),
        ],
        private string $processor = ORMDataProcessor::class,
        private string $provider = ORMDataProvider::class,
    ) {
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getDataClass(): string
    {
        return $this->dataClass;
    }

    public function getTitle(): ?string
    {
        return $this->title;
    }

    public function getGroup(): ?string
    {
        return $this->group;
    }

    public function getIcon(): ?string
    {
        return $this->icon;
    }

    public function getAdminClass(): ?string
    {
        return $this->adminClass;
    }

    public function getActions(): array
    {
        return $this->actions;
    }

    public function getProcessor(): string
    {
        return $this->processor;
    }

    public function getProvider(): string
    {
        return $this->provider;
    }



//    private ?string $currentAction = null;
//
//    public function __construct(
//        private readonly string $name,
//        private readonly string $dataClass,
//        private readonly array $actions = [],
//        private readonly bool $paginated = true,
//        private readonly int $itemPerPage = 25,
//        private readonly array $order = [],
//    ) {
//    }
//
//    public function setCurrentAction(string $actionName): void
//    {
//        $this->currentAction = $actionName;
//    }
//
//    public function getCurrentAction(): Action
//    {
//        return $this->actions[$this->currentAction];
//    }
//
//    public function getName(): string
//    {
//        return $this->name;
//    }
//
//    public function getDataClass(): string
//    {
//        return $this->dataClass;
//    }
//
//    public function getFormType(): ?string
//    {
//        return $this->formType;
//    }
//
//    public function getFormOptions(): array
//    {
//        return $this->formOptions;
//    }
//
//    public function isPaginated(): bool
//    {
//        return $this->paginated;
//    }
//
//    public function getItemPerPage(): int
//    {
//        return $this->itemPerPage;
//    }
//
//    public function getOrder(): array
//    {
//        return $this->order;
//    }


//    private ActionInterface $action;
//    private Request $request;
//    private mixed $data;
//
//    /** @var FormInterface[] */
//    private array $forms = [];
////
////    public function __constructOL(
////        private string $name,
////        private AdminConfiguration $configuration,
////        private EventDispatcherInterface $eventDispatcher
////    ) {
////    }
//
//    public function handleRequest(Request $request)
//    {
//        $this->request = $request;
//        $requestEvent = new RequestEvent($this, $request);
//        // The handleRequest event should be configured the current action
//        $this->eventDispatcher->dispatch($requestEvent, AdminEvents::ADMIN_REQUEST);
//
//        if (!$requestEvent->hasAction()) {
//            throw new Exception('The current action was not set during the dispatch of the event');
//        }
//        $this->action = $requestEvent->getAction();
//
//        // Load data from the database
//        $dataEvent = new DataEvent($this, $request);
//        $this->eventDispatcher->dispatch($dataEvent, AdminEvents::ADMIN_DATA);
//        $this->data = $dataEvent->getData();
//
//        // Create and handle forms
//        $formEvent = new FormEvent($this, $request);
//
//        if ($dataEvent->getFilterForm() !== null) {
//            $formEvent->addForm('filter', $dataEvent->getFilterForm());
//        }
//        $this->eventDispatcher->dispatch($formEvent, AdminEvents::ADMIN_FORM);
//        $this->forms = $formEvent->getForms();
//
//        foreach ($this->forms as $formName => $form) {
//            // The filter form has already been submitted to filter the data
//            if ($formName !== 'filter') {
//                $form->handleRequest($request);
//            }
//        }
//        $this->eventDispatcher->dispatch($formEvent, AdminEvents::ADMIN_HANDLE_FORM);
//    }
//
//    public function getRequest(): Request
//    {
//        if (!isset($this->request)) {
//            throw new Exception('The handleRequest() method should be called before calling getRequest()');
//        }
//
//        return $this->request;
//    }
//
//    public function getName(): string
//    {
//        return $this->name;
//    }
//
//    public function getEntityClass(): string
//    {
//        return $this->configuration->get('entity');
//    }
//
//    public function getEventDispatcher(): EventDispatcherInterface
//    {
//        return $this->eventDispatcher;
//    }
//
//    public function getConfiguration(): AdminConfiguration
//    {
//        return $this->configuration;
//    }
//
//    public function createView(): ViewInterface
//    {
//        if (!isset($this->request)) {
//            throw new Exception('The handleRequest() method should be called before creating a view');
//        }
//        $event = new ViewEvent($this, $this->request);
//        $this->eventDispatcher->dispatch($event, AdminEvents::ADMIN_VIEW);
//
//        return $event->getView();
//    }
//
//    public function getAction(): ActionInterface
//    {
//        return $this->action;
//    }
//
//    public function hasAction(): bool
//    {
//        return isset($this->action);
//    }
//
//    public function getForms(): array
//    {
//        return $this->forms;
//    }
//
//    public function hasForm(string $name): bool
//    {
//        return \array_key_exists($name, $this->forms);
//    }
//
//    public function getForm(string $name): FormInterface
//    {
//        if (!$this->hasForm($name)) {
//            throw new Exception('Form "'.$name.'" does not exists in Admin "'.$this->name.'"');
//        }
//
//        return $this->forms[$name];
//    }
//
//    public function getData()
//    {
//        return $this->data;
//    }
}
