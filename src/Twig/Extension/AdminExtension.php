<?php

declare(strict_types=1);

namespace LAG\AdminBundle\Twig\Extension;

use LAG\AdminBundle\Application\Configuration\ApplicationConfiguration;
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
        private ApplicationConfiguration $applicationConfiguration,
        private Security $security,
        private LinkRendererInterface $linkRenderer,
        private UrlGeneratorInterface $urlGenerator,
        private ResourceRegistryInterface $registry,
    ) {
    }

    public function getFunctions(): array
    {
        return [
            new TwigFunction('lag_admin_config', [$this, 'getConfigurationValue']),
            new TwigFunction('lag_admin_operation_allowed', [$this, 'isOperationAllowed']),
            new TwigFunction('lag_admin_action', [$this, 'renderAction'], ['is_safe' => ['html']]),
            new TwigFunction('lag_admin_operation_url', [$this, 'getOperationUrl'], ['is_safe' => ['html']]),
        ];
    }

    public function getConfigurationValue(string $name): mixed
    {
        return $this->applicationConfiguration->get($name);
    }

    public function isOperationAllowed(string $resourceName, string $operationName): bool
    {
        $operation = $this->registry->get($resourceName)->getOperation($operationName);

        return $this->security->isGranted(OperationPermissionVoter::RESOURCE_ACCESS, $operation);
    }

    /**
     * @param array<string, mixed> $options
     */
    public function renderAction(Link $action, mixed $data = null, array $options = []): string
    {
        return $this->linkRenderer->render($action, $data, $options);
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
