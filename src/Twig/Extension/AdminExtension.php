<?php

namespace LAG\AdminBundle\Twig\Extension;

use LAG\AdminBundle\Configuration\ApplicationConfiguration;
use LAG\AdminBundle\Security\Helper\SecurityHelper;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

class AdminExtension extends AbstractExtension
{
    private bool $mediaEnabled;
    private ApplicationConfiguration $configuration;
    private SecurityHelper $security;

    public function __construct(
        bool $mediaEnabled,
        ApplicationConfiguration $configuration,
        SecurityHelper $security
    ) {
        $this->configuration = $configuration;
        $this->security = $security;
        $this->mediaEnabled = $mediaEnabled;
    }

    public function getFunctions(): array
    {
        return [
            new TwigFunction('admin_config', [$this, 'getApplicationParameter']),
            new TwigFunction('admin_action_allowed', [$this, 'isAdminActionAllowed']),
            new TwigFunction('admin_media_enabled', [$this, 'isMediaBundleEnabled']),
        ];
    }

    public function getApplicationParameter($name)
    {
        return $this
            ->configuration
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
}
