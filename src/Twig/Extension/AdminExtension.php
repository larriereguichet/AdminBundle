<?php

namespace LAG\AdminBundle\Twig\Extension;

use LAG\AdminBundle\Configuration\ApplicationConfiguration;
use LAG\AdminBundle\Security\Helper\SecurityHelper;
use LAG\AdminBundle\Translation\Helper\TranslationHelperInterface;
use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;
use Twig\TwigFunction;

class AdminExtension extends AbstractExtension
{
    private bool $mediaEnabled;
    private ApplicationConfiguration $appConfig;
    private SecurityHelper $security;
    private TranslationHelperInterface $translationHelper;

    public function __construct(
        bool $mediaEnabled,
        ApplicationConfiguration $appConfig,
        SecurityHelper $security,
        TranslationHelperInterface $translationHelper
    ) {
        $this->appConfig = $appConfig;
        $this->security = $security;
        $this->mediaEnabled = $mediaEnabled;
        $this->translationHelper = $translationHelper;
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

    public function getFilters(): array
    {
        return [
            new TwigFilter('admin_trans', [$this, 'translate']),
        ];
    }

    public function getApplicationParameter($name)
    {
        return $this
            ->appConfig
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

    public function translate(
        string $id,
        array $parameters = [],
        string $domain = null,
        string $locale = null,
        string $pattern = null,
        string $adminName = null
    ): string {
        return $this->translationHelper->transWithPattern($id, $parameters, $domain, $locale, $pattern, $adminName);
    }

    public function isTranslationEnabled(): bool
    {
        return $this->appConfig->isTranslationEnabled();
    }
}
