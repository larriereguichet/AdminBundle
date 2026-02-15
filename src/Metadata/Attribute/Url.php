<?php

declare(strict_types=1);

namespace LAG\AdminBundle\Metadata\Attribute;

interface Url
{
    public function getOperation(): ?string;

    public function getRoute(): ?string;

    /** @return array<string, mixed> */
    public function getRouteParameters(): array;

    public function getUrl(): ?string;
}
