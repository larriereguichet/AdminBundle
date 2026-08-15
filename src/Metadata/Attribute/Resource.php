<?php

declare(strict_types=1);

namespace LAG\AdminBundle\Metadata\Attribute;

use LAG\AdminBundle\Bridge\Doctrine\ORM\State\Processor\ORMProcessor;
use LAG\AdminBundle\Bridge\Doctrine\ORM\State\Provider\ORMProvider;
use LAG\AdminBundle\Exception\Exception;
use LAG\AdminBundle\Exception\OperationMissingException;
use LAG\AdminBundle\Exception\Resource\MissingResourceNameException;
use LAG\AdminBundle\Metadata\CollectionOperationInterface;
use LAG\AdminBundle\Metadata\OperationInterface;
use LAG\AdminBundle\Metadata\OperationMetadataInterface;
use LAG\AdminBundle\Metadata\PropertyInterface;
use LAG\AdminBundle\Metadata\ResourceInterface;
use LAG\AdminBundle\Metadata\ResourceMetadataInterface;
use Symfony\Component\Validator\Constraints as Assert;

#[\Attribute(\Attribute::TARGET_CLASS | \Attribute::IS_REPEATABLE)]
class Resource implements ResourceInterface, ResourceMetadataInterface
{
    /**
     * @param array<string>|null $permissions
     * @param array<string, OperationInterface> $operations
     * @param array<string, PropertyInterface> $properties
     * @param array<string>|null $identifiers
     * @param array<string, mixed>|null $formOptions
     * @param array<string, mixed>|null $validationContext
     * @param array<string, mixed>|null $normalizationContext
     * @param array<string, mixed>|null $denormalizationContext
     * @param array<string, mixed> $context
     */
    public function __construct(
        #[Assert\NotBlank(message: 'The resource name should not be null or empty')]
        #[Assert\Regex(
            pattern: '/^[a-z][a-z0-9_]*$/',
            message: 'The resource name should only contain lowercase letters, digits and underscores, and start with a letter',
        )]
        private ?string $shortName = null,

        #[Assert\NotBlank(message: 'The application name should not be empty')]
        #[Assert\Regex(
            pattern: '/^[a-z][a-z0-9_]*$/',
            message: 'The application name should only contain lowercase letters, digits and underscores, and start with a letter',
        )]
        private string $application = 'admin',

        #[Assert\NotBlank(message: 'The resource class should not be null or empty')]
        private ?string $resourceClass = null,

        #[Assert\NotBlank(message: 'The resource title should not be an empty string. Use null instead', allowNull: true)]
        private ?string $title = null,

        #[Assert\NotBlank(message: 'The resource group should not be an empty string. Use null instead', allowNull: true)]
        private ?string $group = null,

        #[Assert\NotBlank(message: 'The resource icon should not be an empty string. Use null instead', allowNull: true)]
        private ?string $icon = null,

        private ?string $pathPrefix = null,

        private ?array $permissions = null,

        /** @var array<int|string, OperationInterface|OperationMetadataInterface> $operations */
        #[Assert\Count(min: 1, minMessage: 'The resource should contains at least one operation')]
        #[Assert\All(constraints: [new Assert\Type(type: OperationInterface::class)])]
        #[Assert\Valid]
        private array $operations = [
            new Index(),
            new Show(),
            new Create(),
            new Update(),
            new Delete(),
        ],

        /** @var array<int|string, PropertyInterface> */
        #[Assert\All(constraints: [new Assert\Type(type: PropertyInterface::class)])]
        #[Assert\Valid]
        private array $properties = [],

        #[Assert\NotBlank]
        private ?string $processor = ORMProcessor::class,

        #[Assert\NotBlank]
        private string $provider = ORMProvider::class,

        private ?array $identifiers = ['id'],

        private ?string $routePattern = '{application}.{resource}.{operation}',

        private ?string $translationPattern = null,

        private ?string $translationDomain = null,

        private ?string $form = null,

        private ?array $formOptions = null,

        #[Assert\NotBlank(message: 'The form template should not be empty. Use null instead', allowNull: true)]
        private ?string $formTemplate = null,

        private bool $validation = true,

        private ?array $validationContext = null,

        private bool $ajax = true,

        #[Assert\NotNull(message: 'The normalization context should not be null. Use an empty array instead')]
        private ?array $normalizationContext = null,

        #[Assert\NotNull(message: 'The denormalization context should not be null. Use an empty array instead')]
        private ?array $denormalizationContext = null,

        private ?string $input = null,

        private ?string $output = null,

        private array $context = [],
    ) {
        foreach ($properties as $index => $property) {
            $this->properties[$property->getName() ?? $index] = $property;
        }
    }

    public function getShortName(): ?string
    {
        return $this->shortName;
    }

    public function getName(): string
    {
        if ($this->shortName === null) {
            throw new MissingResourceNameException();
        }

        return $this->application.'.'.$this->shortName;
    }

    public function withShortName(?string $shortName): self
    {
        $self = clone $this;
        $self->shortName = $shortName;

        return $self;
    }

    public function getResourceClass(): ?string
    {
        return $this->resourceClass;
    }

    public function withResourceClass(?string $resourceClass): self
    {
        $self = clone $this;
        $self->resourceClass = $resourceClass;

        return $self;
    }

    public function getApplication(): string
    {
        return $this->application;
    }

    public function withApplication(?string $application): self
    {
        $self = clone $this;
        $self->application = $application;

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

    /** @return array<string, OperationInterface> */
    public function getOperations(): array
    {
        return $this->operations;
    }

    public function getCollectionOperations(): array
    {
        return array_filter($this->operations, static fn (OperationInterface $operation): bool => $operation instanceof CollectionOperationInterface);
    }

    public function hasOperation(string $operationName): bool
    {
        return array_any($this->operations, static fn (OperationInterface $operation) => $operation->getShortName() === $operationName);
    }

    public function getOperation(string $operationName): OperationInterface
    {
        foreach ($this->operations as $operation) {
            if ($operation->getShortName() === $operationName) {
                return $operation;
            }
        }

        throw new OperationMissingException(
            'The operation with name "%s" does not exists in the resource "%s"',
            $operationName,
            $this->getShortName(),
        );
    }

    /** @param array<OperationMetadataInterface> $operations */
    public function withOperations(array $operations): self
    {
        $self = clone $this;
        $self->operations = [];

        foreach ($operations as $operation) {
            $linked = $operation->setResource($self);
            $self->operations[$linked->getName()] = $linked;
        }

        return $self;
    }

    public function getProperties(): array
    {
        return $this->properties;
    }

    public function getPropertiesByType(string $type): array
    {
        return array_filter($this->properties, static fn (PropertyInterface $property) => $property instanceof $type);
    }

    public function hasProperties(): bool
    {
        return \count($this->properties) > 0;
    }

    public function withProperties(array $properties): self
    {
        $self = clone $this;
        $self->properties = [];

        foreach ($properties as $index => $property) {
            $self->properties[$property->getName() ?? $index] = $property;
        }

        return $self;
    }

    public function hasProperty(string $name): bool
    {
        return \array_key_exists($name, $this->properties);
    }

    public function getProperty(string $name): PropertyInterface
    {
        if (!$this->hasProperty($name)) {
            throw new Exception(
                'The property "%s" does not exists in the resource "%s". Available properties are: %s',
                $name,
                $this->shortName,
                implode(', ', array_keys($this->properties))
            );
        }

        return $this->properties[$name];
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

    public function getPathPrefix(): ?string
    {
        return $this->pathPrefix;
    }

    public function withPathPrefix(?string $prefix): self
    {
        $self = clone $this;
        $self->pathPrefix = $prefix;

        return $self;
    }

    public function getIdentifiers(): ?array
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

    public function getForm(): ?string
    {
        return $this->form;
    }

    public function withForm(?string $form): self
    {
        $self = clone $this;
        $self->form = $form;

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

    public function getFormTemplate(): ?string
    {
        return $this->formTemplate;
    }

    public function withFormTemplate(?string $formTemplate): self
    {
        $self = clone $this;
        $self->formTemplate = $formTemplate;

        return $self;
    }

    public function hasValidation(): bool
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

    public function hasAjax(): bool
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

    public function getRoles(): ?array
    {
        return $this->permissions;
    }

    public function withRoles(array $roles): self
    {
        $self = clone $this;
        $self->permissions = $roles;

        return $self;
    }

    public function getInput(): ?string
    {
        return $this->input;
    }

    public function withInput(?string $input): self
    {
        $self = clone $this;
        $self->input = $input;

        return $self;
    }

    public function getOutput(): ?string
    {
        return $this->output;
    }

    public function withOutput(?string $output): self
    {
        $self = clone $this;
        $self->output = $output;

        return $self;
    }

    public function getContext(): array
    {
        return $this->context;
    }

    public function withContext(array $context): self
    {
        $self = clone $this;
        $self->context = $context;

        return $self;
    }
}
