<?php

declare(strict_types=1);

namespace LAG\AdminBundle\Metadata;

use LAG\AdminBundle\Bridge\Doctrine\ORM\State\ORMDataProcessor;
use LAG\AdminBundle\Bridge\Doctrine\ORM\State\ORMDataProvider;
use LAG\AdminBundle\Form\Type\Resource\FilterType;
use LAG\AdminBundle\Metadata\Filter\FilterInterface;
use LAG\AdminBundle\Validation\Constraint\GridExist;
use LAG\AdminBundle\Validation\Constraint\TemplateValid;

abstract class CollectionOperation extends Operation implements CollectionOperationInterface
{
    public function __construct(
        ?string $name = null,
        ?string $title = null,
        ?string $description = null,
        ?string $icon = null,
        ?string $template = null,
        array $permissions = [],
        ?string $controller = null,
        ?string $route = null,
        array $routeParameters = [],
        array $methods = [],
        string $path = null,
        ?string $redirectRoute = null,
        array $redirectRouteParameters = [],
        array $properties = [],
        ?string $formType = null,
        array $formOptions = [],
        ?string $processor = ORMDataProcessor::class,
        string $provider = ORMDataProvider::class,
        array $identifiers = ['id'],
        ?array $contextualActions = null,
        ?array $itemActions = null,
        ?string $redirectResource = null,
        ?string $redirectOperation = null,
        private bool $pagination = true,
        private int $itemPerPage = 25,
        private string $pageParameter = 'page',
        private array $criteria = [],
        private array $orderBy = [],
        private ?array $filters = null,
        #[GridExist]
        private ?string $grid = 'table',
        private ?string $filterFormType = FilterType::class,
        private array $filterFormOptions = [],
    ) {
        parent::__construct(
            name: $name,
            title: $title,
            description: $description,
            icon: $icon,
            template: $template,
            permissions: $permissions,
            controller: $controller,
            route: $route,
            routeParameters: $routeParameters,
            methods: $methods,
            path: $path,
            redirectRoute: $redirectRoute,
            redirectRouteParameters: $redirectRouteParameters,
            properties: $properties,
            formType: $formType,
            formOptions: $formOptions,
            processor: $processor,
            provider: $provider,
            identifiers: $identifiers,
            contextualActions: $contextualActions,
            itemActions: $itemActions,
            redirectResource: $redirectResource,
            redirectOperation: $redirectOperation,
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

    public function getItemPerPage(): int
    {
        return $this->itemPerPage;
    }

    public function withItemPerPage(int $itemPerPage): self
    {
        $self = clone $this;
        $self->itemPerPage = $itemPerPage;

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

    public function getGrid(): ?string
    {
        return $this->grid;
    }

    public function withGridTemplate(?string $gridTemplate): self
    {
        $self = clone $this;
        $self->grid = $gridTemplate;

        return $self;
    }

    public function getFilterFormType(): ?string
    {
        return $this->filterFormType;
    }

    public function withFilterFormType(?string $filterForm): self
    {
        $self = clone $this;
        $self->filterFormType = $filterForm;

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
}
