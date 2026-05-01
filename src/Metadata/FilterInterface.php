<?php

declare(strict_types=1);

namespace LAG\AdminBundle\Metadata;

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

    /** @return array<string, mixed> */
    public function getFormOptions(): array;

    /** @param array<string, mixed> $formOptions */
    public function withFormOptions(array $formOptions): self;
}
