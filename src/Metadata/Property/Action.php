<?php

declare(strict_types=1);

namespace LAG\AdminBundle\Metadata\Property;

#[\Attribute(\Attribute::TARGET_CLASS | \Attribute::TARGET_PROPERTY | \Attribute::IS_REPEATABLE)]
class Action extends Link
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
