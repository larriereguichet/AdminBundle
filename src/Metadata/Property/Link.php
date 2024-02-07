<?php

declare(strict_types=1);

namespace LAG\AdminBundle\Metadata\Property;

use Symfony\Component\Validator\Constraints as Assert;

#[Assert\Expression(
    expression: 'this.getRoute() or (this.getResourceName() and this.getOperationName()) or this.getUrl()',
    message: 'The link should contains a route or an url or an resource and operation name'
)]
#[\Attribute(\Attribute::IS_REPEATABLE)]
class Link extends Text
{
    public function __construct(
        ?string $name = null,
        string|bool $propertyPath = null,
        ?string $label = null,
        ?string $template = null,
        bool $sortable = true,
        bool $translatable = false,
        ?string $translationDomain = null,
        array $attributes = [],
        array $headerAttributes = [],
        ?string $allowedDataType = null,
        int $length = 100,
        string $replace = '...',
        string $empty = '~',
        string $suffix = '',
        string $prefix = '',

        private ?string $route = null,
        private array $routeParameters = [],
        private ?string $resourceName = null,
        private ?string $operationName = null,
        private ?string $type = null,
        private ?string $url = null,
        private ?string $icon = null,
    ) {
        parent::__construct(
            name: $name,
            propertyPath: true,
            label: $label,
            sortable: $sortable,
            translatable: $translatable,
            translationDomain: $translationDomain,
            attributes: $attributes,
            headerAttributes: $headerAttributes,
            allowedDataType: $allowedDataType,
            length: $length,
            replace: $replace,
            empty: $empty,
            suffix: $suffix,
            prefix: $prefix,
        );
    }

    public function getResourceName(): ?string
    {
        return $this->resourceName;
    }

    public function withResourceName(?string $resourceName): self
    {
        $self = clone $this;
        $self->resourceName = $resourceName;

        return $self;
    }

    public function getRoute(): ?string
    {
        return $this->route;
    }

    public function withRouteName(?string $routeName): self
    {
        $self = clone $this;
        $self->route = $routeName;

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

    public function getOperationName(): ?string
    {
        return $this->operationName;
    }

    public function withOperationName(?string $operationName): self
    {
        $self = clone $this;
        $self->operationName = $operationName;

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
}
