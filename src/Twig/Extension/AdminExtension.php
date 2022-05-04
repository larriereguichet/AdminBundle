<?php

declare(strict_types=1);

namespace LAG\AdminBundle\Twig\Extension;

use LAG\AdminBundle\Application\Configuration\ApplicationConfiguration;
use LAG\AdminBundle\Security\Helper\SecurityHelper;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

class AdminExtension extends AbstractExtension
{
    public function __construct(
        private bool $mediaEnabled,
        private ApplicationConfiguration $applicationConfiguration,
        private SecurityHelper $security
    ) {
    }

    public function getFunctions(): array
    {
        return [
            new TwigFunction('admin_config', [$this, 'getApplicationParameter']),
            new TwigFunction('admin_action_allowed', [$this, 'isAdminActionAllowed']),
            new TwigFunction('admin_media_enabled', [$this, 'isMediaBundleEnabled']),
            new TwigFunction('admin_is_translation_enabled', [$this, 'isTranslationEnabled']),
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
}
