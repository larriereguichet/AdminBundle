<?php

declare(strict_types=1);

namespace LAG\AdminBundle\Metadata;

use LAG\AdminBundle\Bridge\Doctrine\ORM\State\Processor\ORMProcessor;
use LAG\AdminBundle\Bridge\Doctrine\ORM\State\Provider\ORMProvider;
use Symfony\Component\Validator\Constraints as Assert;

abstract class CollectionOperation extends Operation implements CollectionOperationInterface
{
    public function __construct(
        string $name,
        array $context = [],
        ?string $title = null,
        ?string $description = null,
        ?string $icon = null,
        ?string $template = null,
        ?string $baseTemplate = null,
        ?array $permissions = null,
        ?string $controller = null,
        ?string $route = null,
        ?array $routeParameters = null,
        array $methods = [],
        ?string $path = null,
        ?string $redirectRoute = null,
        array $redirectRouteParameters = [],
        ?string $form = null,
        ?array $formOptions = null,
        ?string $formTemplate = null,
        ?string $processor = ORMProcessor::class,
        string $provider = ORMProvider::class,
        ?array $identifiers = null,
        ?array $contextualActions = null,
        ?array $itemActions = null,
        ?string $redirectOperation = null,
        ?bool $validation = true,
        ?array $validationContext = null,
        ?bool $ajax = true,
        ?array $normalizationContext = null,
        ?array $denormalizationContext = null,
        ?string $input = null,
        ?string $output = null,
        ?string $workflow = null,
        ?string $workflowTransition = null,
        bool $partial = false,
        ?string $flashMessage = null,

        private bool $pagination = true,

        #[Assert\GreaterThan(value: 0, message: 'The items per page should be greater than 0')]
        private int $itemsPerPage = 25,

        #[Assert\NotBlank]
        private string $pageParameter = 'page',
        private array $criteria = [], // TODO remove use context
        private array $orderBy = [], // TODO remove use context

        #[Assert\NotNull]
        #[Assert\Valid]
        #[Assert\All(constraints: [new Assert\Type(type: FilterInterface::class)])]
        private ?array $filters = null,

        private ?string $grid = null,

        private array $gridOptions = [],

        #[Assert\NotNull]
        private ?array $collectionActions = null,

        private ?string $filterForm = null,

        private array $filterFormOptions = [],

        #[Assert\NotBlank(allowNull: true, message: 'The collection form type should not be blank. Use null instead')]
        private ?string $collectionForm = null, // TODO remove ?

        #[Assert\NotNull]
        private ?array $collectionFormOptions = null, // TODO remove ?
    ) {
        parent::__construct(
            name: $name,
            context: $context,
            title: $title,
            description: $description,
            icon: $icon,
            template: $template,
            baseTemplate: $baseTemplate,
            permissions: $permissions,
            controller: $controller,
            route: $route,
            routeParameters: $routeParameters,
            methods: $methods,
            path: $path,
            redirectRoute: $redirectRoute,
            redirectRouteParameters: $redirectRouteParameters,
            form: $form,
            formOptions: $formOptions,
            formTemplate: $formTemplate,
            processor: $processor,
            provider: $provider,
            identifiers: $identifiers,
            contextualActions: $contextualActions,
            itemActions: $itemActions,
            redirectOperation: $redirectOperation,
            validation: $validation,
            validationContext: $validationContext,
            ajax: $ajax,
            normalizationContext: $normalizationContext,
            denormalizationContext: $denormalizationContext,
            input: $input,
            output: $output,
            workflow: $workflow,
            workflowTransition: $workflowTransition,
            partial: $partial,
            successMessage: $flashMessage,
        );
    }

    public function hasPagination(): bool
    {
        return $this->pagination;
    }

    public function setPagination(bool $pagination): self
    {
        $self = clone $this;
        $self->pagination = $pagination;

        return $self;
    }

    public function getItemsPerPage(): int
    {
        return $this->itemsPerPage;
    }

    public function withItemsPerPage(int $itemsPerPage): self
    {
        $self = clone $this;
        $self->itemsPerPage = $itemsPerPage;

        return $self;
    }

    public function getPageParameter(): string
    {
        return $this->pageParameter;
    }

    public function withPageParameter(string $pageParameter): self
    {
        $self = clone $this;
        $self->pageParameter = $pageParameter;

        return $self;
    }

    public function getCriteria(): array
    {
        return $this->criteria;
    }

    public function withCriteria(array $criteria): self
    {
        $self = clone $this;
        $self->criteria = $criteria;

        return $self;
    }

    public function getOrderBy(): array
    {
        return $this->orderBy;
    }

    public function withOrderBy(array $orderBy): self
    {
        $self = clone $this;
        $self->orderBy = $orderBy;

        return $self;
    }

    public function getFilters(): ?array
    {
        return $this->filters;
    }

    public function getFilter(string $name): ?FilterInterface
    {
        foreach ($this->filters as $filter) {
            if ($filter->getName() === $name) {
                return $filter;
            }
        }

        return null;
    }

    public function hasFilter(string $name): bool
    {
        foreach ($this->filters as $filter) {
            if ($filter->getName() === $name) {
                return true;
            }
        }

        return false;
    }

    public function hasFilters(): bool
    {
        return $this->filters !== null && \count($this->filters) > 0;
    }

    public function withFilters(array $filters): self
    {
        $self = clone $this;
        $self->filters = $filters;

        return $self;
    }

    public function withFilter(FilterInterface $filter): self
    {
        $self = clone $this;
        $self->filters[] = $filter;

        return $self;
    }

    public function getGrid(): ?string
    {
        return $this->grid;
    }

    public function withGrid(string $grid): self
    {
        $self = clone $this;
        $self->grid = $grid;

        return $self;
    }

    public function withGridOptions(array $gridOptions): self
    {
        $self = clone $this;
        $self->gridOptions = $gridOptions;

        return $self;
    }

    public function getGridOptions(): array
    {
        return $this->gridOptions;
    }

    public function getFilterForm(): ?string
    {
        return $this->filterForm;
    }

    public function withFilterForm(?string $filterForm): self
    {
        $self = clone $this;
        $self->filterForm = $filterForm;

        return $self;
    }

    public function getFilterFormOptions(): array
    {
        return $this->filterFormOptions;
    }

    public function withFilterFormOptions(array $filterFormOptions): self
    {
        $self = clone $this;
        $self->filterFormOptions = $filterFormOptions;

        return $self;
    }

    public function getCollectionForm(): ?string
    {
        return $this->collectionForm;
    }

    public function withCollectionForm(?string $collectionForm): self
    {
        $self = clone $this;
        $self->collectionForm = $collectionForm;

        return $self;
    }

    public function getCollectionFormOptions(): ?array
    {
        return $this->collectionFormOptions;
    }

    public function withCollectionFormOptions(?array $collectionFormOptions): self
    {
        $self = clone $this;
        $self->collectionFormOptions = $collectionFormOptions;

        return $self;
    }

    public function getCollectionActions(): ?array
    {
        return $this->collectionActions;
    }

    public function withCollectionActions(?array $collectionActions): self
    {
        $self = clone $this;
        $self->collectionActions = $collectionActions;

        return $self;
    }
}
