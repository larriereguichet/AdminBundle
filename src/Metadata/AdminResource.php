<?php

declare(strict_types=1);

namespace LAG\AdminBundle\Metadata;

use LAG\AdminBundle\Bridge\Doctrine\ORM\State\Processor\ORMProcessor;
use LAG\AdminBundle\Bridge\Doctrine\ORM\State\Provider\ORMProvider;
use LAG\AdminBundle\Exception\OperationMissingException;
use Symfony\Component\Validator\Constraints as Assert;

#[\Attribute(\Attribute::TARGET_CLASS | \Attribute::IS_REPEATABLE)]
class AdminResource
{
    public function __construct(
        #[Assert\NotBlank(message: 'The resource name should not be null or empty')]
        private ?string $name = null,

        #[Assert\NotBlank(message: 'The data class should not be null or empty')]
        private ?string $dataClass = null,

        #[Assert\NotBlank(allowNull: true, message: 'The resource title should not be an empty string. Use null instead')]
        private ?string $title = null,

        #[Assert\NotBlank(allowNull: true, message: 'The resource group should not be an empty string. Use null instead')]
        private ?string $group = null,

        #[Assert\NotBlank(allowNull: true, message: 'The resource icon should not be an empty string. Use null instead')]
        private ?string $icon = null,

        private ?array $permissions = null,

        /** @var OperationInterface[] $operations */
        #[Assert\Count(min: 1, minMessage: 'The must be at least one operation in the resource')]
        #[Assert\All(constraints: [
            new Assert\Type(type: OperationInterface::class),
        ])]
        #[Assert\Valid]
        private array $operations = [
            new GetCollection(),
            new Get(),
            new Create(),
            new Update(),
            new Delete(),
        ],

        #[Assert\NotBlank]
        private ?string $processor = ORMProcessor::class,

        #[Assert\NotBlank]
        private string $provider = ORMProvider::class,

        /** @var string[] $identifiers */
        private array $identifiers = ['id'],

        private string $routePattern = '{application}.{resource}.{operation}',

        private ?string $routePrefix = '/{resourceName}',

        private ?string $translationPattern = '{application}.{resource}.{message}',

        private ?string $translationDomain = null,

        #[Assert\NotBlank(message: 'The application name should not be empty')]
        private ?string $applicationName = null,

        private ?string $formType = null,

        private array $formOptions = [],

        private bool $validation = true,

        private ?array $validationContext = null,

        private bool $ajax = true,

        #[Assert\NotNull(message: 'The normalization context should not be null. Use an empty array instead')]
        private ?array $normalizationContext = null,

        #[Assert\NotNull(message: 'The denormalization context should not be null. Use an empty array instead')]
        private ?array $denormalizationContext = null,

        private ?string $inputClass = null,

        private ?string $outputClass = null,
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

    public function getDataClass(): ?string
    {
        return $this->dataClass;
    }

    public function withDataClass(?string $dataClass): self
    {
        $self = clone $this;
        $self->dataClass = $dataClass;

        return $self;
    }

    public function getTitle(): ?string
    {
        return $this->title;
    }

    public function withTitle(string $title): self
    {
        $self = clone $this;
        $self->title = $title;

        return $self;
    }

    public function getGroup(): ?string
    {
        return $this->group;
    }

    public function withGroup(string $group): self
    {
        $self = clone $this;
        $self->group = $group;

        return $self;
    }

    public function getIcon(): ?string
    {
        return $this->icon;
    }

    public function withIcon(string $icon): self
    {
        $self = clone $this;
        $self->icon = $icon;

        return $self;
    }

    /**
     * @return OperationInterface[]
     */
    public function getOperations(): array
    {
        return $this->operations;
    }

    public function hasOperation(string $operationName): bool
    {
        foreach ($this->operations as $operation) {
            if ($operation->getName() === $operationName) {
                return true;
            }
        }

        return false;
    }

    public function getOperation(string $operationName): OperationInterface
    {
        foreach ($this->operations as $operation) {
            if ($operation->getName() === $operationName) {
                return $operation;
            }
        }

        throw new OperationMissingException(sprintf('The operation with name "%s" does not exists in the resource "%s"', $operationName, $this->getName()));
    }

    public function withOperations(array $operations): self
    {
        $self = clone $this;
        $self->operations = $operations;

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

    public function getRoutePrefix(): ?string
    {
        return $this->routePrefix;
    }

    public function withRoutePrefix(?string $prefix): self
    {
        $self = clone $this;
        $self->routePrefix = $prefix;

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

    public function getTranslationPattern(): ?string
    {
        return $this->translationPattern;
    }

    public function withTranslationPattern(?string $translationPattern): self
    {
        $self = clone $this;
        $self->translationPattern = $translationPattern;

        return $self;
    }

    public function getTranslationDomain(): ?string
    {
        return $this->translationDomain;
    }

    public function withTranslationDomain(?string $translationDomain): self
    {
        $self = clone $this;
        $self->translationDomain = $translationDomain;

        return $self;
    }

    public function getApplicationName(): ?string
    {
        return $this->applicationName;
    }

    public function withApplicationName(?string $applicationName): self
    {
        $self = clone $this;
        $self->applicationName = $applicationName;

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

    public function getFormOptions(): ?array
    {
        return $this->formOptions;
    }

    public function withFormOptions(?array $formOptions): self
    {
        $self = clone $this;
        $self->formOptions = $formOptions;

        return $self;
    }

    public function isValidationEnabled(): bool
    {
        return $this->validation;
    }

    public function withValidation(bool $validation): self
    {
        $self = clone $this;
        $self->validation = $validation;

        return $self;
    }

    public function getValidationContext(): ?array
    {
        return $this->validationContext;
    }

    public function withValidationContext(array $context): self
    {
        $self = clone $this;
        $self->validationContext = $context;

        return $self;
    }

    public function useAjax(): bool
    {
        return $this->ajax;
    }

    public function withAjax(bool $ajax): self
    {
        $self = clone $this;
        $self->ajax = $ajax;

        return $self;
    }

    public function getNormalizationContext(): ?array
    {
        return $this->normalizationContext;
    }

    public function withNormalizationContext(array $context): self
    {
        $self = clone $this;
        $self->normalizationContext = $context;

        return $self;
    }

    public function getDenormalizationContext(): ?array
    {
        return $this->denormalizationContext;
    }

    public function withDenormalizationContext(array $context): self
    {
        $self = clone $this;
        $self->denormalizationContext = $context;

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

    public function getInputClass(): ?string
    {
        return $this->inputClass;
    }

    public function withInputClass(?string $inputClass): self
    {
        $self = clone $this;
        $self->inputClass = $inputClass;

        return $self;
    }

    public function getOutputClass(): ?string
    {
        return $this->outputClass;
    }

    public function withOutputClass(?string $outputClass): self
    {
        $self = clone $this;
        $self->outputClass = $outputClass;

        return $self;
    }
}
