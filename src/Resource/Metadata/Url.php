<?php

namespace LAG\AdminBundle\Resource\Metadata;

interface Url
{
    public function getApplication(): ?string;

    public function getResource(): ?string;

    public function getOperation(): ?string;

    public function getRoute(): ?string;

    public function getRouteParameters(): array;

    public function getUrl(): ?string;
}
