<?php

declare(strict_types=1);

namespace LAG\AdminBundle\Metadata\Attribute;

use LAG\AdminBundle\Workflow\WorkflowSubjectInterface;
use Symfony\Component\Validator\Constraints as Assert;

#[Assert\Expression(
    expression: 'this.getRoute() or this.getOperation() or this.getUrl()',
    message: 'The link should contains a route or an url or an resource and operation name'
)]
#[\Attribute(\Attribute::TARGET_CLASS | \Attribute::TARGET_PROPERTY | \Attribute::IS_REPEATABLE)]
class Action extends Property implements Url, WorkflowSubjectInterface
{
    /** @param array<string, mixed> $routeParameters */
    public function __construct(
        ?string $name = null,
        string|bool|null $propertyPath = true,
        string|bool|null $label = null,
        ?string $template = '@LAGAdmin/grids/properties/action.html.twig',
        bool $translatable = true,
        ?string $translationDomain = null,
        array $attributes = [],
        array $rowAttributes = [],
        array $headerAttributes = [],
        ?string $dataTransformer = null,
        ?array $permissions = null,
        ?string $condition = null,
        ?string $sortingPath = null,

        private ?string $route = null,
        private array $routeParameters = [],
        private ?string $operation = null,
        private ?string $url = null,
        private ?string $type = null,
        private ?string $icon = null,
        private ?string $workflow = null,
        private ?string $title = null,
    ) {
        parent::__construct(
            name: $name,
            propertyPath: $propertyPath,
            label: $label,
            template: $template,
            sortable: false,
            translatable: $translatable,
            translationDomain: $translationDomain,
            attributes: $attributes,
            rowAttributes: $rowAttributes,
            headerAttributes: $headerAttributes,
            dataTransformer: $dataTransformer,
            permissions: $permissions,
            condition: $condition,
            sortingPath: $sortingPath,
        );
    }

    public function getRoute(): ?string
    {
        return $this->route;
    }

    public function withRoute(?string $routeName): self
    {
        $self = clone $this;
        $self->route = $routeName;

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

    public function getIcon(): ?string
    {
        return $this->icon;
    }

    public function withIcon(?string $icon): self
    {
        $self = clone $this;
        $self->icon = $icon;

        return $this;
    }

    public function getWorkflow(): ?string
    {
        return $this->workflow;
    }

    public function setWorkflow(?string $workflow): self
    {
        $self = clone $this;
        $self->workflow = $workflow;

        return $self;
    }

    public function getTitle(): ?string
    {
        return $this->title;
    }

    public function withTitle(?string $title): self
    {
        $self = clone $this;
        $self->title = $title;

        return $self;
    }
}
