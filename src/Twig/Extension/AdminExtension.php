<?php

declare(strict_types=1);

namespace LAG\AdminBundle\Twig\Extension;

use LAG\AdminBundle\Grid\Render\LinkRendererInterface;
use LAG\AdminBundle\Resource\Metadata\Link;
use LAG\AdminBundle\Resource\Registry\ResourceRegistryInterface;
use LAG\AdminBundle\Routing\UrlGenerator\UrlGeneratorInterface;
use LAG\AdminBundle\Security\Voter\OperationPermissionVoter;
use Symfony\Bundle\SecurityBundle\Security;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

class AdminExtension extends AbstractExtension
{
    public function __construct(
        private readonly Security $security,
        private readonly LinkRendererInterface $linkRenderer,
        private readonly UrlGeneratorInterface $urlGenerator,
        private readonly ResourceRegistryInterface $registry,
    ) {
    }

    public function getFunctions(): array
    {
        return [
            new TwigFunction('lag_admin_operation_allowed', [$this, 'isOperationAllowed']),
            new TwigFunction('lag_admin_link', [$this, 'renderLink'], ['is_safe' => ['html']]),
            new TwigFunction('lag_admin_link_url', [$this, 'generateLinkUrl']),
        ];
    }

    public function isOperationAllowed(string $resourceName, string $operationName, ?string $applicationName = null): bool
    {
        $operation = $this->registry->get($resourceName, $applicationName)->getOperation($operationName);

        return $this->security->isGranted(OperationPermissionVoter::RESOURCE_ACCESS, $operation);
    }

    public function generateLinkUrl(Link $link, mixed $data = null): string
    {
        return $this->urlGenerator->generateUrl($link, $data);
    }

    /**
     * @param array<string, mixed> $options
     */
    public function renderLink(Link $link, mixed $data = null, array $options = []): string
    {
        return $this->linkRenderer->render($link, $data, $options);
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
