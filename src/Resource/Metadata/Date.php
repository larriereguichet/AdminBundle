<?php

declare(strict_types=1);

namespace LAG\AdminBundle\Resource\Metadata;

use Symfony\Component\Validator\Constraints as Assert;

#[\Attribute(\Attribute::TARGET_CLASS | \Attribute::TARGET_PROPERTY | \Attribute::IS_REPEATABLE)]
class Date extends Property
{
    public function __construct(
        ?string $name = null,
        ?string $propertyPath = null,
        string|bool|null $label = null,
        bool $sortable = true,
        bool $translatable = false,
        ?string $translationDomain = null,
        array $attributes = [],
        array $headerAttributes = [],
        ?string $dataTransformer = null,
        ?array $permissions = null,
        ?string $condition = null,

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
            dataTransformer: $dataTransformer,
            permissions: $permissions,
            condition: $condition,
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
