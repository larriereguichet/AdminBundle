<?php

declare(strict_types=1);

namespace LAG\AdminBundle\Response\Handler;

use LAG\AdminBundle\Resource\Metadata\OperationInterface;
use LAG\AdminBundle\Routing\UrlGenerator\UrlGeneratorInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Response;

final readonly class RedirectHandler implements RedirectHandlerInterface
{
    public function __construct(
        private UrlGeneratorInterface $urlGenerator,
    ) {
    }

    public function createRedirectResponse(OperationInterface $operation, mixed $data, array $context = []): Response
    {
        if ($operation->getRedirectOperation()) {
            $redirectUrl = $this->urlGenerator->generateFromOperationName(
                $operation->getRedirectResource() ?? $operation->getResource()->getName(),
                $operation->getRedirectOperation(),
                $data,
                $operation->getRedirectApplication() ?? $operation->getResource()->getApplication(),
            );
        } elseif ($operation->getRedirectRoute()) {
            $redirectUrl = $this->urlGenerator->generateFromRouteName(
                $operation->getRedirectRoute(),
                $operation->getRedirectRouteParameters(),
                $data,
            );
        } else {
            $redirectUrl = $this->urlGenerator->generateFromRouteName(
                $operation->getRoute(),
                $operation->getRouteParameters(),
                $data,
            );
        }

        return new RedirectResponse($redirectUrl);
    }
}
