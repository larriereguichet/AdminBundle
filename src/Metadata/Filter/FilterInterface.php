<?php

declare(strict_types=1);

namespace LAG\AdminBundle\Metadata\Filter;

interface FilterInterface
{
    public function getName(): string;

    public function withName(string $name): self;

    public function getPropertyPath(): ?string;

    public function withPropertyPath(string $propertyPath): self;

    public function getComparator(): string;

    public function withComparator(string $comparator): self;

    public function getOperator(): string;

    public function withOperator(string $operator): self;

    public function getData(): mixed;

    public function withData(mixed $data): self;

    public function getFormType(): string;

    public function withFormType(string $formType): self;

    public function getFormOptions(): array;

    public function withFormOptions(array $formOptions): self;
}
