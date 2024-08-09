<?php

declare(strict_types=1);

namespace LAG\AdminBundle\View\Helper;

use LAG\AdminBundle\Resource\Metadata\Url;

interface RoutingHelperInterface
{
    public function generatePath(
        string $resource,
        string $operation,
        mixed $data = null,
        ?string $applicationName = null,
    ): string;

    public function generateResourceUrl(Url $url, mixed $data = null): string;

    public function generateUrl(
        string $resource,
        string $operation,
        mixed $data = null,
        ?string $applicationName = null,
    ): string;
}
