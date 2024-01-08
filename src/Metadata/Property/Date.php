<?php

declare(strict_types=1);

namespace LAG\AdminBundle\Metadata\Property;

use Symfony\Component\Validator\Constraints as Assert;

#[\Attribute]
class Date extends Property
{
    public function __construct(
        ?string $name = null,
        ?string $propertyPath = null,
        ?string $label = null,
        bool $sortable = true,
        bool $translatable = false,
        ?string $translationDomain = null,
        array $attributes = [],
        array $headerAttributes = [],
        ?string $allowedDataType = null,
        array $grids = [],

        #[Assert\NotBlank(message: 'The date format should not be empty. Use "none" instead')]
        private string $dateFormat = 'medium',

        #[Assert\NotBlank(message: 'The time format should not be empty. Use "none" instead')]
        private string $timeFormat = 'none',
    ) {
        parent::__construct(
            name: $name,
            propertyPath: $propertyPath,
            label: $label,
            template: '@LAGAdmin/grids/properties/date.html.twig',
            sortable: $sortable,
            translatable: $translatable,
            translationDomain: $translationDomain,
            attributes: $attributes,
            headerAttributes: $headerAttributes,
            allowedDataType: $allowedDataType,
            grids: $grids,
        );
    }

    public function getDateFormat(): string
    {
        return $this->dateFormat;
    }

    public function withDateFormat(string $dateFormat): self
    {
        $self = clone $this;
        $self->dateFormat = $dateFormat;

        return $self;
    }

    public function getTimeFormat(): string
    {
        return $this->timeFormat;
    }

    public function withTimeFormat(string $timeFormat): self
    {
        $self = clone $this;
        $self->timeFormat = $timeFormat;

        return $self;
    }
}
