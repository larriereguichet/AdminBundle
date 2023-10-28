<?php

declare(strict_types=1);

namespace LAG\AdminBundle\Metadata\Property;

use Symfony\Component\PropertyAccess\PropertyAccess;

class Link extends Text implements ConfigurablePropertyInterface
{
    public function __construct(
        string $name,
        string $propertyPath = null,
        string $label = null,
        ?string $template = '@LAGAdmin/grids/properties/link.html.twig',
        bool $sortable = true,
        bool $translatable = false,
        string $translationDomain = null,
        array $attr = [],
        array $headerAttr = [],
        int $length = 100,
        string $replace = '...',
        string $emptyString = '~',
        private ?string $route = null,
        private ?array $routeParameters = [],
    ) {
        parent::__construct(
            name: $name,
            propertyPath: $propertyPath,
            label: $label,
            template: $template,
            sortable: $sortable,
            translatable: $translatable,
            translationDomain: $translationDomain,
            attr: $attr,
            headerAttr: $headerAttr,
            length: $length,
            replace: $replace,
            emptyString: $emptyString,
        );
    }

    public function configure(mixed $data): void
    {
        $accessor = PropertyAccess::createPropertyAccessor();

        foreach ($this->routeParameters as $name => $value) {
            if ($value !== null || !$accessor->isReadable(objectOrArray: $data, propertyPath: $name)) {
                continue;
            }
            $this->routeParameters[$name] = $accessor->getValue(objectOrArray: $data, propertyPath: $name);
        }
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

    public function getRouteParameters(): ?array
    {
        return $this->routeParameters;
    }

    public function setRouteParameters(?array $routeParameters): self
    {
        $self = clone $this;
        $self->routeParameters = $routeParameters;

        return $self;
    }
}
