<?php

declare(strict_types=1);

namespace LAG\AdminBundle\Twig\Extension;

use LAG\AdminBundle\Action\Render\ActionRendererInterface;
use LAG\AdminBundle\Application\Configuration\ApplicationConfiguration;
use LAG\AdminBundle\Metadata\Action;
use LAG\AdminBundle\Metadata\OperationInterface;
use LAG\AdminBundle\Routing\UrlGenerator\UrlGeneratorInterface;
use LAG\AdminBundle\Security\Helper\SecurityHelper;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

class AdminExtension extends AbstractExtension
{
    public function __construct(
        private bool $mediaEnabled,
        private ApplicationConfiguration $applicationConfiguration,
        private SecurityHelper $security,
        private ActionRendererInterface $actionRenderer,
        private UrlGeneratorInterface $urlGenerator,
    ) {
    }

    public function getFunctions(): array
    {
        return [
            new TwigFunction('admin_config', [$this, 'getApplicationParameter']),
            new TwigFunction('admin_action_allowed', [$this, 'isAdminActionAllowed']),
            new TwigFunction('admin_media_enabled', [$this, 'isMediaBundleEnabled']),
            new TwigFunction('admin_is_translation_enabled', [$this, 'isTranslationEnabled']),
            new TwigFunction('lag_admin_action', [$this, 'renderAction'], ['is_safe' => ['html']]),
            new TwigFunction('lag_admin_operation_url', [$this, 'getOperationUrl'], ['is_safe' => ['html']]),
        ];
    }

    public function getApplicationParameter($name)
    {
        return $this
            ->applicationConfiguration
            ->get($name)
        ;
    }

    /**
     * Return true if the given action is allowed for the given Admin.
     */
    public function isAdminActionAllowed(string $adminName, string $actionName): bool
    {
        return $this->security->isActionAllowed($adminName, $actionName);
    }

    /**
     * Return true if the media bundle is enabled.
     */
    public function isMediaBundleEnabled(): bool
    {
        return $this->mediaEnabled;
    }

    public function isTranslationEnabled(): bool
    {
        return $this->applicationConfiguration->isTranslationEnabled();
    }

    public function renderAction(Action $action, mixed $data = null, array $options = []): string
    {
        return $this->actionRenderer->render($action, $data, $options);
    }

    public function getOperationUrl(OperationInterface $operation, mixed $data = null): string
    {
        return $this->urlGenerator->generateFromOperationName(
            $operation->getResourceName(),
            $operation->getName(),
            $data,
        );
    }
}
