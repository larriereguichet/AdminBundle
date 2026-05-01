<?php

declare(strict_types=1);

namespace LAG\AdminBundle\Metadata\Attribute;

use LAG\AdminBundle\Metadata\ApplicationInterface;
use LAG\AdminBundle\Metadata\ApplicationMetadataInterface;
use Symfony\Component\Validator\Constraints as Assert;

class Application implements ApplicationInterface, ApplicationMetadataInterface
{
    public function __construct(
        #[Assert\NotBlank(message: 'The application name should not be blank')]
        private string $name,

        #[Assert\NotBlank(message: 'The application date format should not be blank')]
        private string $dateFormat = 'd/m/Y',

        #[Assert\NotBlank(message: 'The application time format should not be blank')]
        private string $timeFormat = 'H:i',

        #[Assert\NotBlank(message: 'The application translation domain should not be blank')]
        private string $translationDomain = 'messages',

        #[Assert\NotBlank(message: 'The application translation pattern should not be blank')]
        private string $translationPattern = '{application}.{resource}.{message}',

        #[Assert\NotBlank(message: 'The application route pattern should not be blank')]
        private string $routePattern = '{application}.{resource}.{operation}',

        #[Assert\NotBlank(message: 'The application base template should not be blank')]
        private string $baseTemplate = '@LAGAdmin/base.html.twig',

        /** @var array<int, string> $permissions */
        private array $permissions = ['ROLE_ADMIN'],
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

    public function getTranslationDomain(): string
    {
        return $this->translationDomain;
    }

    public function withTranslationDomain(string $translationDomain): self
    {
        $self = clone $this;
        $self->translationDomain = $translationDomain;

        return $self;
    }

    public function getTranslationPattern(): string
    {
        return $this->translationPattern;
    }

    public function withTranslationPattern(string $translationPattern): self
    {
        $self = clone $this;
        $self->translationPattern = $translationPattern;

        return $self;
    }

    public function getRoutePattern(): string
    {
        return $this->routePattern;
    }

    public function withRoutePattern(string $routePattern): self
    {
        $self = clone $this;
        $self->routePattern = $routePattern;

        return $self;
    }

    public function getBaseTemplate(): string
    {
        return $this->baseTemplate;
    }

    public function withBaseTemplate(string $baseTemplate): self
    {
        $self = clone $this;
        $self->baseTemplate = $baseTemplate;

        return $self;
    }

    public function getPermissions(): array
    {
        return $this->permissions;
    }

    /** @param string[] $permissions */
    public function withPermissions(array $permissions): self
    {
        $self = clone $this;
        $self->permissions = $permissions;

        return $self;
    }
}
