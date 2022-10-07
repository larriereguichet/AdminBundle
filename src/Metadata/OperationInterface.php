<?php

namespace LAG\AdminBundle\Metadata;

use LAG\AdminBundle\Field\Field;
use LAG\AdminBundle\Metadata\Property\PropertyInterface;

interface OperationInterface
{
    public function getName(): ?string;

    public function withName(?string $name): self;

    public function getResourceName(): ?string;

    public function withResourceName(?string $resourceName): self;

    public function getTitle(): ?string;

    public function withTitle(?string $title): self;

    public function getDescription(): ?string;

    public function withDescription(?string $description): self;

    public function getIcon(): ?string;

    public function withIcon(?string $icon): self;

    public function getTemplate(): ?string;

    public function withTemplate(?string $template): self;

    public function getPermissions(): ?array;

    public function withPermissions(?array $permissions): self;

    public function getController(): ?string;

    public function withController(?string $controller): self;

    public function getRoute(): ?string;

    public function withRoute(?string $route): self;

    public function getRouteParameters(): ?array;

    public function withRouteParameters(?array $routeParameters): self;

    public function getPath(): ?string;

    public function withPath(?string $path): self;

    public function getTargetRoute(): ?string;

    public function withTargetRoute(?string $targetRoute): self;

    public function getTargetRouteParameters(): ?array;

    public function withTargetRouteParameters(?array $targetRouteParameters): self;

    /**
     * @return PropertyInterface[]
     */
    public function getProperties(): array;

    public function withProperties(array $properties): self;

    public function getFormType(): ?string;

    public function withFormType(?string $formType): self;

    public function getFormOptions(): array;

    public function withFormOptions(array $formOptions): self;

    public function getProcessor(): string;

    public function withProcessor(string $processor): self;

    public function getProvider(): string;

    public function withProvider(string $provider): self;

    public function getMethods(): array;

    public function withMethods(array $methods): self;

    public function getIdentifiers(): array;

    public function withIdentifiers(array $identifiers): self;

    public function getResource(): AdminResource;

    public function withResource(AdminResource $resource): self;

    public function getItemActions(): ?array;

    public function withItemActions(array $itemActions): self;

}
