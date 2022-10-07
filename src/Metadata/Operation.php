<?php

declare(strict_types=1);

namespace LAG\AdminBundle\Metadata;

use LAG\AdminBundle\Bridge\Doctrine\ORM\State\ORMDataProcessor;
use LAG\AdminBundle\Bridge\Doctrine\ORM\State\ORMDataProvider;
use Symfony\Component\Validator\Constraints as Assert;

abstract class Operation implements OperationInterface
{
    private AdminResource $resource;

    public function __construct(
        #[Assert\NotBlank(message: 'The operation name should not be empty')]
        private ?string $name = null,
        #[Assert\NotBlank(message: 'The resource name should not be empty')]
        private ?string $resourceName = null,
        #[Assert\Length(max: 255, maxMessage: 'The operation title should be shorter than 255 characters')]
        private ?string $title = null,
        private ?string $description = null,
        #[Assert\Length(max: 255, maxMessage: 'The operation icon should be shorter than 255 characters')]
        private ?string $icon = null,
        #[Assert\NotBlank(message: 'The operation template should not be empty')]
        private ?string $template = null,
        private ?array $permissions = [],
        #[Assert\NotBlank(message: 'The operation controller should not be empty')]
        private ?string $controller = null,
        #[Assert\NotBlank(message: 'The operation has an empty route')]
        private ?string $route = null,
        private ?array $routeParameters = null,
        private array $methods = [],
        private ?string $path = null,
        private ?string $targetRoute = null,
        private ?array $targetRouteParameters = null,
        private array $properties = [],
        private ?string $formType = null,
        private array $formOptions = [],
        private string $processor = ORMDataProcessor::class,
        private string $provider = ORMDataProvider::class,
        private array $identifiers = ['id'],
        private ?array $itemActions = null,
    ) {
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function withName(?string $name): self
    {
        $self = clone $this;
        $self->name = $name;

        return $self;
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

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function withDescription(?string $description): self
    {
        $self = clone $this;
        $self->description = $description;

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

    public function getPermissions(): ?array
    {
        return $this->permissions;
    }

    public function withPermissions(?array $permissions): self
    {
        $self = clone $this;
        $self->permissions = $permissions;

        return $self;
    }

    public function getController(): ?string
    {
        return $this->controller;
    }

    public function withController(?string $controller): self
    {
        $self = clone $this;
        $self->controller = $controller;

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

    public function getRouteParameters(): ?array
    {
        return $this->routeParameters;
    }

    public function withRouteParameters(?array $routeParameters): self
    {
        $self = clone $this;
        $self->routeParameters = $routeParameters;

        return $self;
    }

    public function getPath(): ?string
    {
        return $this->path;
    }

    public function withPath(?string $path): self
    {
        $self = clone $this;
        $self->path = $path;

        return $self;
    }

    public function getTargetRoute(): ?string
    {
        return $this->targetRoute;
    }

    public function withTargetRoute(?string $targetRoute): self
    {
        $self = clone $this;
        $self->targetRoute = $targetRoute;

        return $self;
    }

    public function getTargetRouteParameters(): ?array
    {
        return $this->targetRouteParameters;
    }

    public function withTargetRouteParameters(?array $targetRouteParameters): self
    {
        $self = clone $this;
        $self->targetRouteParameters = $targetRouteParameters;

        return $self;
    }

    public function getProperties(): array
    {
        return $this->properties;
    }

    public function withProperties(array $properties): self
    {
        $self = clone $this;
        $self->properties = $properties;

        return $self;
    }

    public function getFormType(): ?string
    {
        return $this->formType;
    }

    public function withFormType(?string $formType): self
    {
        $self = clone $this;
        $self->formType = $formType;

        return $self;
    }

    public function getFormOptions(): array
    {
        return $this->formOptions;
    }

    public function withFormOptions(array $formOptions): self
    {
        $self = clone $this;
        $self->formOptions = $formOptions;

        return $self;
    }

    public function getProcessor(): string
    {
        return $this->processor;
    }

    public function withProcessor(string $processor): self
    {
        $self = clone $this;
        $self->processor = $processor;

        return $self;
    }

    public function getProvider(): string
    {
        return $this->provider;
    }

    public function withProvider(string $provider): self
    {
        $self = clone $this;
        $self->provider = $provider;

        return $self;
    }

    public function getMethods(): array
    {
        return $this->methods;
    }

    public function withMethods(array $methods): self
    {
        $self = clone $this;
        $self->methods = $methods;

        return $self;
    }

    public function getIdentifiers(): array
    {
        return $this->identifiers;
    }

    public function withIdentifiers(array $identifiers): self
    {
        $self = clone $this;
        $self->identifiers = $identifiers;

        return $self;
    }

    public function getResource(): AdminResource
    {
        return $this->resource;
    }

    public function withResource(AdminResource $resource): self
    {
        $self = clone $this;
        $self->resource = $resource;

        return $self;
    }

    public function getItemActions(): ?array
    {
        return $this->itemActions;
    }

    public function withItemActions(array $itemActions): self
    {
        $self = clone $this;
        $self->itemActions = $itemActions;

        return $self;
    }
}
