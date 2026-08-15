<?php

declare(strict_types=1);

namespace LAG\AdminBundle\Metadata\Attribute;

use LAG\AdminBundle\Bridge\Doctrine\ORM\State\Processor\ORMProcessor;
use LAG\AdminBundle\Bridge\Doctrine\ORM\State\Provider\ORMProvider;
use LAG\AdminBundle\Metadata\CollectionOperationInterface;
use LAG\AdminBundle\Metadata\CollectionOperationMetadataInterface;
use LAG\AdminBundle\Metadata\FilterInterface;
use Symfony\Component\Validator\Constraints as Assert;

#[Assert\Expression(
    expression: 'this.getLimit() === null or not this.hasPagination()',
    message: 'Pagination must be disabled when a limit is set.',
)]
abstract class CollectionOperation extends Operation implements CollectionOperationInterface, CollectionOperationMetadataInterface
{
    /**
     * @param array<string, mixed> $context
     * @param array<string>|null $permissions
     * @param array<string, mixed>|null $routeParameters
     * @param array<string> $methods
     * @param array<string, string> $orderBy
     * @param array<string, mixed> $redirectRouteParameters
     * @param array<string, mixed>|null $formOptions
     * @param array<string>|null $identifiers
     * @param array<string, Link>|null $contextualLinks
     * @param array<string, Link>|null $itemLinks
     * @param array<string, mixed>|null $validationContext
     * @param array<string, mixed>|null $normalizationContext
     * @param array<string, mixed>|null $denormalizationContext
     * @param array<int|string, FilterInterface>|null $filters
     * @param array<string, mixed> $gridOptions
     * @param array<string, Link>|null $collectionLinks
     * @param array<string, mixed> $filterFormOptions
     * @param string[] $batchOperations
     */
    public function __construct(
        string $name,
        array $context = [],
        string|false|null $title = null,
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
        ?array $contextualLinks = null,
        ?array $itemLinks = null,
        ?string $redirectOperation = null,
        ?bool $validation = true,
        ?array $validationContext = null,
        ?bool $ajax = true,
        ?array $normalizationContext = null,
        ?array $denormalizationContext = null,
        ?string $input = null,
        ?string $output = null,
        ?string $normalizationInput = null,
        ?string $normalizationOutput = null,
        ?string $workflow = null,
        ?string $workflowTransition = null,
        bool $embedded = false,
        ?string $flashMessage = null,

        #[Assert\Positive]
        private ?int $limit = null,

        private bool $pagination = true,

        #[Assert\GreaterThan(value: 0, message: 'The items per page should be greater than 0')]
        private int $itemsPerPage = 25,

        #[Assert\NotBlank]
        private string $pageParameter = 'page',

        private array $orderBy = [],

        #[Assert\NotNull]
        #[Assert\Valid]
        #[Assert\All(constraints: [new Assert\Type(type: FilterInterface::class)])]
        private ?array $filters = null,

        private ?string $grid = null,

        private array $gridOptions = [],

        #[Assert\NotNull]
        private ?array $collectionLinks = null,

        private ?string $filterForm = null,

        private array $filterFormOptions = [],

        #[Assert\All(constraints: [new Assert\NotBlank()])]
        private array $batchOperations = [],
    ) {
        parent::__construct(
            shortName: $name,
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
            contextualLinks: $contextualLinks,
            itemLinks: $itemLinks,
            redirectOperation: $redirectOperation,
            validation: $validation,
            validationContext: $validationContext,
            ajax: $ajax,
            normalizationContext: $normalizationContext,
            denormalizationContext: $denormalizationContext,
            input: $input,
            output: $output,
            normalizationInput: $normalizationInput,
            normalizationOutput: $normalizationOutput,
            workflow: $workflow,
            workflowTransition: $workflowTransition,
            embedded: $embedded,
            successMessage: $flashMessage,
        );
    }

    public function getLimit(): ?int
    {
        return $this->limit;
    }

    public function withLimit(?int $limit): static
    {
        $self = clone $this;
        $self->limit = $limit;

        return $self;
    }

    public function hasPagination(): bool
    {
        return $this->pagination;
    }

    public function withPagination(bool $pagination): static
    {
        $self = clone $this;
        $self->pagination = $pagination;

        return $self;
    }

    public function getItemsPerPage(): int
    {
        return $this->itemsPerPage;
    }

    public function withItemsPerPage(int $itemsPerPage): static
    {
        $self = clone $this;
        $self->itemsPerPage = $itemsPerPage;

        return $self;
    }

    public function getPageParameter(): string
    {
        return $this->pageParameter;
    }

    public function withPageParameter(string $pageParameter): static
    {
        $self = clone $this;
        $self->pageParameter = $pageParameter;

        return $self;
    }

    public function getOrderBy(): array
    {
        return $this->orderBy;
    }

    /** @param array<string, string> $orderBy */
    public function withOrderBy(array $orderBy): static
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

    public function withFilters(array $filters): static
    {
        $self = clone $this;
        $self->filters = $filters;

        return $self;
    }

    public function withFilter(FilterInterface $filter): static
    {
        $self = clone $this;
        $self->filters[] = $filter;

        return $self;
    }

    public function getGrid(): ?string
    {
        return $this->grid;
    }

    public function withGrid(string $grid): static
    {
        $self = clone $this;
        $self->grid = $grid;

        return $self;
    }

    public function withGridOptions(array $gridOptions): static
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

    public function withFilterForm(?string $filterForm): static
    {
        $self = clone $this;
        $self->filterForm = $filterForm;

        return $self;
    }

    public function getFilterFormOptions(): array
    {
        return $this->filterFormOptions;
    }

    public function withFilterFormOptions(array $filterFormOptions): static
    {
        $self = clone $this;
        $self->filterFormOptions = $filterFormOptions;

        return $self;
    }

    public function getBatchOperations(): array
    {
        return $this->batchOperations;
    }

    /** @param string[] $batchOperations */
    public function withBatchOperations(array $batchOperations): static
    {
        $self = clone $this;
        $self->batchOperations = $batchOperations;

        return $self;
    }

    public function getCollectionLinks(): ?array
    {
        return $this->collectionLinks;
    }

    public function withCollectionLinks(?array $collectionLinks): static
    {
        $self = clone $this;
        $self->collectionLinks = $collectionLinks;

        return $self;
    }
}
