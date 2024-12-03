<?php

declare(strict_types=1);

namespace LAG\AdminBundle\View\Helper;

use LAG\AdminBundle\Resource\Context\ResourceContextInterface;
use LAG\AdminBundle\Resource\Metadata\Link;
use LAG\AdminBundle\Resource\Metadata\Url;
use LAG\AdminBundle\Routing\UrlGenerator\UrlGeneratorInterface;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface as SymfonyUrlGeneratorInterface;
use Twig\Extension\RuntimeExtensionInterface;

final readonly class RoutingHelper implements RuntimeExtensionInterface
{
    public function __construct(
        private ResourceContextInterface $resourceContext,
        private RequestStack $requestStack,
        private UrlGeneratorInterface $urlGenerator,
    ) {
    }

    public function generatePath(
        string $resource,
        string $operation,
        mixed $data = null,
        ?string $application = null,
        int $referenceType = SymfonyUrlGeneratorInterface::ABSOLUTE_PATH,
    ): string {
        if ($application === null) {
            $request = $this->requestStack->getCurrentRequest();
            $application = $this->resourceContext->getOperation($request)->getResource()->getApplication();
        }

        return $this->urlGenerator->generateFromOperationName($resource, $operation, $data, $application, $referenceType);
    }

    public function generateResourceUrl(
        Url $url,
        mixed $data = null,
        int $referenceType = SymfonyUrlGeneratorInterface::ABSOLUTE_PATH,
    ): string {
        return $this->urlGenerator->generateFromUrl($url, $data, $referenceType);
    }

    public function generateUrl(
        string $resource,
        string $operation,
        mixed $data = null,
        ?string $application = null,
        int $referenceType = SymfonyUrlGeneratorInterface::ABSOLUTE_PATH,
    ): string {
        if ($application === null) {
            $request = $this->requestStack->getCurrentRequest();
            $application = $this->resourceContext->getOperation($request)->getResource()->getApplication();
        }

        return $this->urlGenerator->generateFromOperationName(
            $resource,
            $operation,
            $data,
            $application,
            $referenceType,
        );
    }

    public function generateLinkUrl(
        Link $link,
        mixed $data = null,
        int $referenceType = SymfonyUrlGeneratorInterface::ABSOLUTE_PATH,
    ): string {
        return $this->urlGenerator->generateFromUrl($link, $data, $referenceType);
    }
}
