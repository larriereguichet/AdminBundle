<?php

declare(strict_types=1);

namespace LAG\AdminBundle\Resource\Metadata;

use Symfony\Component\Validator\Constraints as Assert;

#[Assert\Expression(
    expression: 'this.getRoute() or (this.getResource() and this.getOperation()) or this.getUrl()',
    message: 'The link should contains a route or an url or an resource and operation name'
)]
#[\Attribute(\Attribute::TARGET_CLASS | \Attribute::TARGET_PROPERTY | \Attribute::IS_REPEATABLE)]
class Link extends Property implements Url
{
    public function __construct(
        ?string $name = null,
        string|bool|null $propertyPath = null,
        string|bool|null $label = null,
        ?string $template = '@LAGAdmin/grids/properties/link.html.twig',
        bool $sortable = false,
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
        private ?string $application = null,
        private ?string $resource = null,
        private ?string $operation = null,
        private ?string $type = null,
        private ?string $url = null,
        private ?string $text = null,
        private ?string $textPath = null,
        private ?string $icon = null,
    ) {
        parent::__construct(
            name: $name,
            propertyPath: $propertyPath,
            label: $label,
            template: $template,
            sortable: $sortable,
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

    public function getApplication(): ?string
    {
        return $this->application;
    }

    public function withApplication(?string $application): self
    {
        $self = clone $this;
        $self->application = $application;

        return $self;
    }

    public function getResource(): ?string
    {
        return $this->resource;
    }

    public function withResource(?string $resource): self
    {
        $self = clone $this;
        $self->resource = $resource;

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

    public function getRouteParameters(): array
    {
        return $this->routeParameters;
    }

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
}
