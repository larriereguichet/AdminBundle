<?php

declare(strict_types=1);

namespace LAG\AdminBundle\Twig\Runtime;

use LAG\AdminBundle\Metadata\Attribute\Link;
use LAG\AdminBundle\Resource\Factory\OperationFactoryInterface;
use LAG\AdminBundle\Routing\UrlGenerator\LinkUrlGeneratorInterface;
use LAG\AdminBundle\Routing\UrlGenerator\OperationUrlGeneratorInterface;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Twig\Extension\RuntimeExtensionInterface;

final readonly class RoutingRuntime implements RuntimeExtensionInterface
{
    public function __construct(
        private OperationFactoryInterface $operationFactory,
        private OperationUrlGeneratorInterface $urlGenerator,
        private LinkUrlGeneratorInterface $linkUrlGenerator,
    ) {
    }

    public function generatePath(string $operationName, mixed $data = null): string
    {
        $operation = $this->operationFactory->create($operationName);

        return $this->urlGenerator->generateUrl($operation, $data);
    }

    public function generateUrl(string $operationName, mixed $data = null): string
    {
        $operation = $this->operationFactory->create($operationName);

        return $this->urlGenerator->generateUrl($operation, $data, UrlGeneratorInterface::ABSOLUTE_URL);
    }

    public function generateLink(Link $link, mixed $data = null, int $referenceType = UrlGeneratorInterface::ABSOLUTE_PATH): string
    {
        return $this->linkUrlGenerator->generateUrl($link, $data, $referenceType);
    }
}
