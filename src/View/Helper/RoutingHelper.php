<?php

declare(strict_types=1);

namespace LAG\AdminBundle\View\Helper;

use LAG\AdminBundle\Metadata\Link;
use LAG\AdminBundle\Metadata\Url;
use LAG\AdminBundle\Routing\UrlGenerator\ResourceUrlGeneratorInterface;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Twig\Extension\RuntimeExtensionInterface;

final readonly class RoutingHelper implements RuntimeExtensionInterface
{
    public function __construct(
        private ResourceUrlGeneratorInterface $urlGenerator,
    ) {
    }

    public function generatePath(string $operationName, mixed $data = null): string
    {
        return $this->urlGenerator->generateFromOperationName($operationName, $data);
    }

    public function generateUrl(string $operationName, mixed $data = null): string
    {
        return $this->urlGenerator->generateFromOperationName($operationName, $data, UrlGeneratorInterface::ABSOLUTE_URL);
    }

    public function generateResourceUrl(
        Url $url,
        mixed $data = null,
        int $referenceType = UrlGeneratorInterface::ABSOLUTE_PATH,
    ): string {
        return $this->urlGenerator->generateFromUrl($url, $data, $referenceType);
    }

    public function generateLinkUrl(
        Link $link,
        mixed $data = null,
        int $referenceType = UrlGeneratorInterface::ABSOLUTE_PATH,
    ): string {
        return $this->urlGenerator->generateFromUrl($link, $data, $referenceType);
    }
}
