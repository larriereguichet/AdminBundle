<?php

declare(strict_types=1);

namespace LAG\AdminBundle\Resource\Metadata;

interface FilterInterface
{
    public function getName(): string;

    public function withName(string $name): self;

    public function getComparator(): string;

    public function withComparator(string $comparator): self;

    public function getOperator(): string;

    public function withOperator(string $operator): self;

    public function getFormType(): string;

    public function withFormType(string $formType): self;

    public function getFormOptions(): array;

    public function withFormOptions(array $formOptions): self;

    public function getLabel(): ?string;

    public function withLabel(?string $label): self;
}
