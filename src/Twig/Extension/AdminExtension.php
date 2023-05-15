<?php

declare(strict_types=1);

namespace LAG\AdminBundle\Twig\Extension;

use LAG\AdminBundle\Application\Configuration\ApplicationConfiguration;
use LAG\AdminBundle\Grid\View\LinkRendererInterface;
use LAG\AdminBundle\Metadata\Link;
use LAG\AdminBundle\Metadata\OperationInterface;
use LAG\AdminBundle\Routing\UrlGenerator\UrlGeneratorInterface;
use LAG\AdminBundle\Security\Helper\SecurityHelper;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

class AdminExtension extends AbstractExtension
{
    public function __construct(
        private bool $mediaBundleEnabled,
        private ApplicationConfiguration $applicationConfiguration,
        private SecurityHelper $security,
        private LinkRendererInterface $actionRenderer,
        private UrlGeneratorInterface $urlGenerator,
    ) {
    }

    public function getFunctions(): array
    {
        return [
            new TwigFunction('lag_admin_config', [$this, 'getConfigurationValue']),
            new TwigFunction('lag_admin_operation_allowed', [$this, 'isOperationAllowed']),
            new TwigFunction('lag_admin_action', [$this, 'renderAction'], ['is_safe' => ['html']]),
            new TwigFunction('lag_admin_operation_url', [$this, 'getOperationUrl'], ['is_safe' => ['html']]),
            new TwigFunction('lag_admin_media_bundle_enabled', [$this, 'isMediaBundleEnabled']),
        ];
    }

    public function getConfigurationValue(string $name): mixed
    {
        return $this->applicationConfiguration->get($name);
    }

    public function isOperationAllowed(string $resourceName, string $operationName): bool
    {
        return $this->security->isOperationAllowed($resourceName, $operationName);
    }

    /**
     * @param array<string, mixed> $options
     */
    public function renderAction(Link $action, mixed $data = null, array $options = []): string
    {
        return $this->actionRenderer->render($action, $data, $options);
    }

    public function getOperationUrl(OperationInterface $operation, mixed $data = null): string
    {
        return $this->urlGenerator->generateFromOperationName(
            $operation->getResource()->getName(),
            $operation->getName(),
            $data,
        );
    }

    public function isMediaBundleEnabled(): bool
    {
        return $this->mediaBundleEnabled;
    }
}
