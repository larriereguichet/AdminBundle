<?php

declare(strict_types=1);

namespace LAG\AdminBundle\View\Helper;

use LAG\AdminBundle\Resource\Context\ResourceContextInterface;
use LAG\AdminBundle\Resource\Metadata\Url;
use LAG\AdminBundle\Routing\UrlGenerator\UrlGeneratorInterface;
use Symfony\Component\HttpFoundation\RequestStack;

final readonly class RoutingHelper implements RoutingHelperInterface
{
    public function __construct(
        private ResourceContextInterface $context,
        private RequestStack $requestStack,
        private UrlGeneratorInterface $urlGenerator,
    ) {
    }

    public function generatePath(
        string $resource,
        string $operation,
        mixed $data = null,
        ?string $applicationName = null,
    ): string {
        if ($applicationName === null) {
            $request = $this->requestStack->getCurrentRequest();
            $applicationName = $this->context->getOperation($request)->getResource()->getApplication();
        }

        return $this->urlGenerator->generateFromOperationName($resource, $operation, $data, $applicationName);
    }

    public function generateResourceUrl(Url $url, mixed $data = null): string
    {
        return $this->urlGenerator->generateUrl($url, $data);
    }

    public function generateUrl(
        string $resource,
        string $operation,
        mixed $data = null,
        ?string $applicationName = null,
    ): string {
        return $this->urlGenerator->generateFromOperationName(
            $resource,
            $operation,
            $data,
            $applicationName,
        );
    }
}
