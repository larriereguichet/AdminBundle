<?php

declare(strict_types=1);

namespace LAG\AdminBundle\Twig\Extension;

use LAG\AdminBundle\Grid\View\LinkRendererInterface;
use LAG\AdminBundle\Metadata\Link;
use LAG\AdminBundle\Metadata\Registry\ResourceRegistryInterface;
use LAG\AdminBundle\Routing\UrlGenerator\UrlGeneratorInterface;
use LAG\AdminBundle\Security\Voter\OperationPermissionVoter;
use Symfony\Bundle\SecurityBundle\Security;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

class AdminExtension extends AbstractExtension
{
    public function __construct(
        private Security $security,
        private LinkRendererInterface $linkRenderer,
        private UrlGeneratorInterface $urlGenerator,
        private ResourceRegistryInterface $registry,
    ) {
    }

    public function getFunctions(): array
    {
        return [
            new TwigFunction('lag_admin_operation_allowed', [$this, 'isOperationAllowed']),
            new TwigFunction('lag_admin_link', [$this, 'renderLink'], ['is_safe' => ['html']]),
            new TwigFunction('lag_admin_operation_url', [$this, 'getOperationUrl'], ['is_safe' => ['html']]),
        ];
    }

    public function isOperationAllowed(string $resourceName, string $operationName): bool
    {
        $operation = $this->registry->get($resourceName)->getOperation($operationName);

        return $this->security->isGranted(OperationPermissionVoter::RESOURCE_ACCESS, $operation);
    }

    /**
     * @param array<string, mixed> $options
     */
    public function renderLink(Link $link, mixed $data = null, array $options = []): string
    {
        return $this->linkRenderer->render($link, $data, $options);
    }

    public function getOperationUrl(
        string $resource,
        string $operation,
        mixed $data = null
    ): string {
        return $this->urlGenerator->generateFromOperationName(
            $resource,
            $operation,
            $data,
        );
    }
}
