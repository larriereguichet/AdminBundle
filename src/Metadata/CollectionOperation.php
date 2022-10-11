<?php

declare(strict_types=1);

namespace LAG\AdminBundle\Metadata;

use LAG\AdminBundle\Bridge\Doctrine\ORM\State\ORMDataProcessor;
use LAG\AdminBundle\Bridge\Doctrine\ORM\State\ORMDataProvider;
use LAG\AdminBundle\Metadata\Filter\FilterInterface;

abstract class CollectionOperation extends Operation implements CollectionOperationInterface
{
    public function __construct(
        ?string $name = null,
        ?string $resourceName = null,
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
        ?string $targetRoute = null,
        array $targetRouteParameters = [],
        array $properties = [],
        ?string $formType = null,
        array $formOptions = [],
        string $processor = ORMDataProcessor::class,
        string $provider = ORMDataProvider::class,
        array $identifiers = ['id'],
        ?array $itemActions = null,
        private bool $pagination = true,
        private int $itemPerPage = 25,
        private string $pageParameter = 'page',
        private array $criteria = [],
        private array $orderBy = [],
        private ?array $filters = null,
        private ?array $listActions = null,
        private ?string $gridTemplate = '@LAGAdmin/grid/table_grid.html.twig',
    ) {
        parent::__construct(
            $name,
            $resourceName,
            $title,
            $description,
            $icon,
            $template,
            $permissions,
            $controller,
            $route,
            $routeParameters,
            $methods,
            $path,
            $targetRoute,
            $targetRouteParameters,
            $properties,
            $formType,
            $formOptions,
            $processor,
            $provider,
            $identifiers,
            $itemActions,
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
        return $this->filters !== null && count($this->filters) > 0;
    }

    public function withFilters(array $filters): CollectionOperationInterface
    {
        $self = clone $this;
        $self->filters = $filters;

        return $self;
    }

    public function getListActions(): ?array
    {
        return $this->listActions;
    }

    public function withListActions(array $listActions): self
    {
        $self = clone $this;
        $self->listActions = $listActions;

        return $self;
    }

    public function getGridTemplate(): ?string
    {
        return $this->gridTemplate;
    }

    public function withGridTemplate(?string $gridTemplate): self
    {
        $self = clone $this;
        $self->gridTemplate = $gridTemplate;

        return $self;
    }
}
