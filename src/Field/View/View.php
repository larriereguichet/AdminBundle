<?php

declare(strict_types=1);

namespace LAG\AdminBundle\Field\View;

use Closure;

interface View
{
    public function getName(): string;

    public function getOptions(): array;

    public function getOption(string $name);

    public function getTemplate(): string;

    public function getDataTransformer(): ?Closure;
}
