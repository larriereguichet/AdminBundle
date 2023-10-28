<?php

declare(strict_types=1);

namespace LAG\AdminBundle\Response\Handler;

use LAG\AdminBundle\Metadata\OperationInterface;
use LAG\AdminBundle\Routing\UrlGenerator\UrlGeneratorInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Response;

class RedirectHandler implements RedirectHandlerInterface
{
    public function __construct(
        private UrlGeneratorInterface $urlGenerator,
    ) {
    }

    public function createRedirectResponse(OperationInterface $operation, mixed $data): Response
    {
        if ($operation->getRedirectResource() && $operation->getRedirectOperation()) {
            $redirectUrl = $this->urlGenerator->generateFromOperationName(
                $operation->getRedirectResource(),
                $operation->getRedirectOperation(),
                $data,
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
