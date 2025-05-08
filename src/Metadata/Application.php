<?php

declare(strict_types=1);

namespace LAG\AdminBundle\Metadata;

class Application
{
    public function __construct(
        private string $name,
        private ?string $dateFormat = null,
        private ?string $timeFormat = null,
        private ?string $translationDomain = null,
        private ?string $translationPattern = null,
        private ?string $routePattern = null,
        private ?string $baseTemplate = null,
        /** @var array<int, string>|null $permissions */
        private ?array $permissions = null,
    ) {
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function withName(string $name): self
    {
        $self = clone $this;
        $self->name = $name;

        return $self;
    }

    public function getDateFormat(): ?string
    {
        return $this->dateFormat;
    }

    public function withDateFormat(string $dateFormat): self
    {
        $self = clone $this;
        $self->dateFormat = $dateFormat;

        return $self;
    }

    public function getTimeFormat(): ?string
    {
        return $this->timeFormat;
    }

    public function withTimeFormat(string $timeFormat): self
    {
        $self = clone $this;
        $self->timeFormat = $timeFormat;

        return $self;
    }

    public function getTranslationDomain(): ?string
    {
        return $this->translationDomain;
    }

    public function withTranslationDomain(string $translationDomain): self
    {
        $self = clone $this;
        $self->translationDomain = $translationDomain;

        return $self;
    }

    public function getTranslationPattern(): ?string
    {
        return $this->translationPattern;
    }

    public function withTranslationPattern(string $translationPattern): self
    {
        $self = clone $this;
        $self->translationPattern = $translationPattern;

        return $self;
    }

    public function getRoutePattern(): ?string
    {
        return $this->routePattern;
    }

    public function withRoutePattern(string $routePattern): self
    {
        $self = clone $this;
        $self->routePattern = $routePattern;

        return $self;
    }

    public function getBaseTemplate(): ?string
    {
        return $this->baseTemplate;
    }

    public function withBaseTemplate(string $baseTemplate): self
    {
        $self = clone $this;
        $self->baseTemplate = $baseTemplate;

        return $self;
    }

    public function getPermissions(): ?array
    {
        return $this->permissions;
    }

    public function withPermissions(array $permissions): self
    {
        $self = clone $this;
        $self->permissions = $permissions;

        return $self;
    }
}
