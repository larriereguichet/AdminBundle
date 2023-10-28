<?php

declare(strict_types=1);

namespace LAG\AdminBundle\Grid;

interface GridInterface
{
    public function getName(): string;

    public function getTemplate(): string;

    public function getTemplateMapping(): array;

    public function getOptions(): array;
}
