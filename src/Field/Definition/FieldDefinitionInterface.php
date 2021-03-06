<?php

declare(strict_types=1);

namespace LAG\AdminBundle\Field\Definition;

interface FieldDefinitionInterface
{
    public function getType(): ?string;

    public function getOptions(): array;

    public function getFormOptions(): array;
}
