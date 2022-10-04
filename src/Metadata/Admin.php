<?php

namespace LAG\AdminBundle\Metadata;

use LAG\AdminBundle\Bridge\Doctrine\ORM\ORMDataProcessor;
use LAG\AdminBundle\Bridge\Doctrine\ORM\ORMDataProvider;
use LAG\AdminBundle\Exception\Operation\OperationMissingException;
use Symfony\Component\Validator\Constraints as Assert;

#[\Attribute(\Attribute::TARGET_CLASS | \Attribute::IS_REPEATABLE)]
class Admin
{
    private OperationInterface $currentOperation;

    public function __construct(
        private ?string $name = null,
        private ?string $dataClass = null,
        private ?string $title = null,
        private ?string $group = null,
        private ?string $icon = null,
        /** @var OperationInterface[] $operations */
        #[Assert\Valid()]
        private array $operations = [
            new Index(),
            new Create(),
            new Update(),
            new Delete(),
            new Show(),
        ],
        private string $processor = ORMDataProcessor::class,
        private string $provider = ORMDataProvider::class,
        /** @var string[] $identifiers */
        private array $identifiers = ['id'],
        private string $routePattern = 'lag_admin.{resource}.{operation}',
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

    public function getOperation(string $operationName): Operation
    {
        foreach ($this->operations as $operation) {
            if ($operation->getName() === $operationName) {
                return $operation;
            }
        }

        throw new OperationMissingException(sprintf(
            'The operation with name "%s" does not exists in the resource "%s"',
            $operationName,
            $this->getName(),
        ));
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

    public function getCurrentOperation(): OperationInterface
    {
        return $this->currentOperation;
    }

    public function withCurrentOperation(OperationInterface $currentOperation): self
    {
        $self = clone $this;
        $self->currentOperation = $currentOperation;

        return $self;
    }
}
