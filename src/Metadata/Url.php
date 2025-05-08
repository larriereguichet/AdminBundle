<?php

declare(strict_types=1);

namespace LAG\AdminBundle\Metadata;

interface Url
{
    public function getOperation(): ?string;

    public function getRoute(): ?string;

    public function getRouteParameters(): array;

    public function getUrl(): ?string;
}
