<?php

declare(strict_types=1);

namespace LAG\AdminBundle\Translation\Helper;

use LAG\AdminBundle\Admin\Helper\AdminHelperInterface;
use LAG\AdminBundle\Configuration\ApplicationConfiguration;
use Symfony\Component\String\UnicodeString;
use Symfony\Contracts\Translation\TranslatorInterface;

class TranslationHelper implements TranslationHelperInterface
{
    private TranslatorInterface $translator;
    private ApplicationConfiguration $appConfig;
    private AdminHelperInterface $adminHelper;

    public function __construct(
        TranslatorInterface $translator,
        ApplicationConfiguration $appConfig,
        AdminHelperInterface $adminHelper
    ) {
        $this->translator = $translator;
        $this->appConfig = $appConfig;
        $this->adminHelper = $adminHelper;
    }

    /**
     * Return the translation pattern with keys "{admin}" and "{key}" replaced by their values.
     */
    public static function getTranslationKey(
        string $translationPattern,
        string $adminName,
        string $key
    ): string {
        $u = new UnicodeString($key);
        $u = $u->snake();
        $translationPattern = str_replace('{key}', $u->toString(), $translationPattern);

        return str_replace('{admin}', $adminName, $translationPattern);
    }

    public function trans(string $id, array $parameters = [], string $domain = null, string $locale = null): string
    {
        return $this->translator->trans($id, $parameters, $domain, $locale);
    }

    public function transWithPattern(
        string $id,
        array $parameters = [],
        string $domain = null,
        string $locale = null,
        string $pattern = null,
        string $adminName = null
    ): string {
        if ($pattern === null) {
            $pattern = $this->appConfig->getTranslationPattern();
        }

        if ($domain === null) {
            $domain = $this->appConfig->getTranslationCatalog();
        }

        if ($adminName === null) {
            $adminName = $this->adminHelper->getAdmin()->getName();
        }
        $id = self::getTranslationKey(
            $pattern,
            $adminName,
            $id
        );

        return $this->trans($id, [], $domain, $locale);
    }
}
