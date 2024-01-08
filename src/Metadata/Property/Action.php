<?php

declare(strict_types=1);

namespace LAG\AdminBundle\Metadata\Property;

use Symfony\Component\PropertyAccess\PropertyAccess;

#[\Attribute]
class Action extends Text implements ConfigurableProperty
{
    public function __construct(
        ?string $name = null,
        string $label = null,
        bool $translatable = false,
        string $translationDomain = null,
        array $attributes = [],
        array $headerAttributes = [],
        int $length = 100,
        string $replace = '...',
        string $emptyString = '~',
        private ?string $route = null,
        private ?array $routeParameters = [],
    ) {
        parent::__construct(
            name: $name,
            propertyPath: '.',
            label: $label,
            template: '@LAGAdmin/grids/properties/action.html.twig',
            sortable: false,
            translatable: $translatable,
            translationDomain: $translationDomain,
            attributes: $attributes,
            headerAttributes: $headerAttributes,
            length: $length,
            replace: $replace,
            empty: $emptyString,
        );
    }

    public function configure(mixed $data): void
    {
        $accessor = PropertyAccess::createPropertyAccessor();

        foreach ($this->routeParameters as $name => $value) {
            if ($value !== null || !$accessor->isReadable($data, $name)) {
                continue;
            }
            $this->routeParameters[$name] = $accessor->getValue($data, $name);
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
