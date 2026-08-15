<?php

declare(strict_types=1);

namespace LAG\AdminBundle\Twig\Component;

interface AttributeComponentInterface
{
    /** @return array<string, mixed> */
    public function getAttributes(): array;
}
