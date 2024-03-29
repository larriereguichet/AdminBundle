<?php

declare(strict_types=1);

namespace LAG\AdminBundle\Metadata;

use Symfony\Component\Validator\Constraints as Assert;

#[Assert\Expression(
    expression: 'this.route or (this.resourceName and this.operationName) or this.url',
    message: 'The link should contains a route or an url or an resource and operation name'
)]
class Link
{
    public function __construct(
        private ?string $route = null,
        private array $routeParameters = [],
        private ?string $resourceName = null,
        private ?string $operationName = null,
        private ?string $template = '@LAGAdmin/grids/actions/link.html.twig',
        private ?string $label = null,
        private ?string $type = null,
        private ?string $url = null,
        private ?string $icon = null,
    ) {
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

    public function withName(?string $routeName): self
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

    public function getTemplate(): ?string
    {
        return $this->template;
    }

    public function withTemplate(?string $template): self
    {
        $self = clone $this;
        $self->template = $template;

        return $self;
    }

    public function getLabel(): ?string
    {
        return $this->label;
    }

    public function withLabel(?string $label): self
    {
        $self = clone $this;
        $self->label = $label;

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
