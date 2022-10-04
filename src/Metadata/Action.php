<?php

namespace LAG\AdminBundle\Metadata;

class Action
{
    public function __construct(
        private ?string $routeName = null,
        private array $routeParameters = [],
        private ?string $resourceName = null,
        private ?string $operationName = null,
        private ?string $template = '@LAGAdmin/grid/actions/button.html.twig',
        private ?string $label = null,
        private ?string $type = null,
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

    public function getRouteName(): ?string
    {
        return $this->routeName;
    }

    public function withRouteName(?string $routeName): self
    {
        $self = clone $this;
        $self->routeName = $routeName;

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
}
