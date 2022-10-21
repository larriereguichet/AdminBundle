<?php

namespace LAG\AdminBundle\Metadata\Property;

class DateProperty extends AbstractProperty
{
    public function __construct(
        string $name,
        ?string $propertyPath = null,
        ?string $label = null,
        ?string $template = '@LAGAdmin/fields/date.html.twig',
        bool $mapped = true,
        bool $sortable = true,
        bool $translation = false,
        ?string $translationDomain = 'admin',
        array $attr = [],
        array $headerAttr = [],
        private string $dateFormat = 'medium',
        private string $timeFormat = 'short',
        private bool $localization = true,
    ) {
        parent::__construct(
            $name,
            $propertyPath,
            $label,
            $template,
            $mapped,
            $sortable,
            $translation,
            $translationDomain,
            $attr,
            $headerAttr,
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

    public function isLocalization(): bool
    {
        return $this->localization;
    }

    public function withLocalization(bool $localization): self
    {
        $self = clone $this;
        $self->localization = $localization;

        return $self;
    }
}
