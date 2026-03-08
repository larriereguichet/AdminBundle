<?php

declare(strict_types=1);

namespace LAG\AdminBundle\Routing\UrlGenerator;

interface UrlGeneratorInterface
{
    /**
     * @param array<string, mixed> $routeParameters
     */
    public function generateUrl(
        string $routeName,
        array $routeParameters = [],
        mixed $data = null,
        int $referenceType = \Symfony\Component\Routing\Generator\UrlGeneratorInterface::ABSOLUTE_PATH,
    ): string;
}
