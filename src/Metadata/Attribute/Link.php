<?php

declare(strict_types=1);

namespace LAG\AdminBundle\Metadata\Attribute;

use LAG\AdminBundle\Workflow\WorkflowAwareInterface;
use Symfony\Component\Validator\Constraints as Assert;

#[Assert\Expression(
    expression: 'this.getRoute() or this.getOperation() or this.getUrl()',
    message: 'The link should contains a route or an url or an resource and operation name'
)]
#[\Attribute(\Attribute::TARGET_CLASS | \Attribute::TARGET_PROPERTY | \Attribute::IS_REPEATABLE)]
class Link extends Property implements WorkflowAwareInterface
{
    /** @param array<string, mixed> $routeParameters */
    public function __construct(
        ?string $name = null,
        string|bool|null $propertyPath = null,
        string|bool|null $label = null,
        ?string $template = null,
        bool $sortable = false,
        bool $translatable = true,

        array $attributes = [],
        array $rowAttributes = [],
        array $headerAttributes = [],
        ?string $dataTransformer = null,
        ?array $permissions = null,
        ?string $condition = null,
        ?string $sortingPath = null,
        ?string $component = 'lag_admin:link',
        ?string $translationDomain = null,

        private ?string $route = null,
        private array $routeParameters = [],
        private ?string $operation = null,
        private ?string $type = null,
        private ?string $url = null,
        private ?string $text = null,
        private ?string $textPath = null,
        private ?string $icon = null,
        private ?string $workflow = null,
        private ?string $workflowTransition = null,
    ) {
        parent::__construct(
            name: $name,
            propertyPath: $propertyPath,
            label: $label,
            template: $template,
            sortable: $sortable,
            translatable: $translatable,
            attributes: $attributes,
            rowAttributes: $rowAttributes,
            headerAttributes: $headerAttributes,
            dataTransformer: $dataTransformer,
            permissions: $permissions,
            condition: $condition,
            sortingPath: $sortingPath,
            component: $component,
            translationDomain: $translationDomain,
        );
    }

    public function getOperation(): ?string
    {
        return $this->operation;
    }

    public function withOperation(?string $operation): self
    {
        $self = clone $this;
        $self->operation = $operation;

        return $self;
    }

    public function getRoute(): ?string
    {
        return $this->route;
    }

    public function withRoute(?string $route): self
    {
        $self = clone $this;
        $self->route = $route;

        return $self;
    }

    /** @return array<string, mixed> */
    public function getRouteParameters(): array
    {
        return $this->routeParameters;
    }

    /** @param array<string, mixed> $routeParameters */
    public function withRouteParameters(array $routeParameters): self
    {
        $self = clone $this;
        $self->routeParameters = $routeParameters;

        return $self;
    }

    public function getType(): ?string
    {
        return $this->type;
    }

    public function withType(?string $type): self
    {
        $self = clone $this;
        $self->type = $type;

        return $self;
    }

    public function getUrl(): ?string
    {
        return $this->url;
    }

    public function withUrl(?string $url): self
    {
        $self = clone $this;
        $self->url = $url;

        return $self;
    }

    public function getText(): ?string
    {
        return $this->text;
    }

    public function withText(?string $text): self
    {
        $self = clone $this;
        $self->text = $text;

        return $self;
    }

    public function getIcon(): ?string
    {
        return $this->icon;
    }

    public function withIcon(?string $icon): self
    {
        $self = clone $this;
        $self->icon = $icon;

        return $self;
    }

    public function getTextPath(): ?string
    {
        return $this->textPath;
    }

    public function withTextPath(?string $textPath): self
    {
        $self = clone $this;
        $self->textPath = $textPath;

        return $self;
    }

    public function getWorkflow(): ?string
    {
        return $this->workflow;
    }

    public function withWorkflow(?string $workflow): self
    {
        $self = clone $this;
        $self->workflow = $workflow;

        return $self;
    }

    public function getWorkflowTransition(): ?string
    {
        return $this->workflowTransition;
    }

    public function withWorkflowTransition(?string $workflowTransition): self
    {
        $self = clone $this;
        $self->workflowTransition = $workflowTransition;

        return $self;
    }
}
