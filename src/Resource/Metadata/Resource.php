<?php

declare(strict_types=1);

namespace LAG\AdminBundle\Resource\Metadata;

use LAG\AdminBundle\Bridge\Doctrine\ORM\State\Processor\ORMProcessor;
use LAG\AdminBundle\Bridge\Doctrine\ORM\State\Provider\ORMProvider;
use LAG\AdminBundle\Exception\Exception;
use LAG\AdminBundle\Exception\OperationMissingException;
use Symfony\Component\Validator\Constraints as Assert;

#[\Attribute(\Attribute::TARGET_CLASS | \Attribute::IS_REPEATABLE)]
class Resource
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
        #[Assert\All(constraints: [new Assert\Type(type: OperationInterface::class)])]
        #[Assert\Valid]
        private array $operations = [
            new Index(),
            new Get(),
            new Create(),
            new Update(),
            new Delete(),
        ],

        /** @var PropertyInterface[] */
        #[Assert\All(constraints: [new Assert\Type(type: PropertyInterface::class)])]
        #[Assert\Valid]
        private array $properties = [],

        #[Assert\NotBlank]
        private ?string $processor = ORMProcessor::class,

        #[Assert\NotBlank]
        private string $provider = ORMProvider::class,

        /** @var string[] $identifiers */
        private array $identifiers = ['id'],

        private string $routePattern = '{application}.{resource}.{operation}',

        private ?string $pathPrefix = null,

        private ?string $translationPattern = null,

        private ?string $translationDomain = null,

        #[Assert\NotBlank(message: 'The application name should not be empty')]
        private ?string $application = null,

        private ?string $form = null,

        private array $formOptions = [],

        #[Assert\NotBlank(allowNull: true, message: 'The form template should not be empty. Use null instead')]
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

        private array $grids = [],
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

    /** @return OperationInterface[] */
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

    public function hasOperationOfType(string $operationClass): bool
    {
        foreach ($this->operations as $operation) {
            if ($operation instanceof $operationClass) {
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

        throw new OperationMissingException(\sprintf('The operation with name "%s" does not exists in the resource "%s"', $operationName, $this->getName()));
    }

    public function getOperationOfType(string $operationClass): OperationInterface
    {
        foreach ($this->operations as $operation) {
            if ($operation instanceof $operationClass) {
                return $operation;
            }
        }

        throw new OperationMissingException(\sprintf('The operation of type "%s" does not exists in the resource "%s"', $operationClass, $this->getName()));
    }

    public function withOperations(array $operations): self
    {
        $self = clone $this;
        $self->operations = $operations;

        return $self;
    }

    /** @return array<int, PropertyInterface> */
    public function getProperties(): array
    {
        return $this->properties;
    }

    public function hasProperties(): bool
    {
        return \count($this->properties) > 0;
    }

    public function withProperties(array $properties): self
    {
        $self = clone $this;

        foreach ($properties as $property) {
            $self->properties[$property->getName()] = $property;
        }

        return $self;
    }

    public function withProperty(PropertyInterface $property): self
    {
        $self = clone $this;
        $self->properties[$property->getName()] = $property;

        return $self;
    }

    public function hasProperty(string $name): bool
    {
        return \array_key_exists($name, $this->properties);
    }

    public function getProperty(string $name): PropertyInterface
    {
        if (!$this->hasProperty($name)) {
            throw new Exception('The property "'.$name.'" does not exists in the resource "'.$this->name.'"');
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

    public function getApplication(): ?string
    {
        return $this->application;
    }

    public function withApplication(?string $application): self
    {
        $self = clone $this;
        $self->application = $application;

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

    public function getGrids(): array
    {
        return $this->grids;
    }

    public function hasGrid(string $name): bool
    {
        foreach ($this->grids as $grid) {
            if ($grid->getName() === $name) {
                return true;
            }
        }

        return false;
    }

    public function withGrid(Grid $grid): self
    {
        $self = clone $this;
        $self->grids[$grid->getName()] = $grid;

        return $self;
    }
}
