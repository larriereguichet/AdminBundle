<?php

declare(strict_types=1);

namespace LAG\AdminBundle\Translation\Helper;

use LAG\AdminBundle\Application\Configuration\ApplicationConfiguration;
use LAG\AdminBundle\Admin\Helper\AdminContextInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

class TranslationHelper implements TranslationHelperInterface
{
    public function __construct(
        private TranslatorInterface $translator,
        private ApplicationConfiguration $applicationConfiguration,
        private AdminContextInterface $adminHelper
    ) {
    }

    public function getTranslationKey(string $key , string $adminName = 'ui'): string
    {
        $translationPattern = $this->applicationConfiguration->getTranslationPattern();
        $translationPattern = str_replace('{key}', $key, $translationPattern);

        return str_replace('{admin}', $adminName, $translationPattern);
    }

    public function translate(string $id, array $parameters = [], string $domain = null, string $locale = null): string
    {
        if ($domain === null) {
            $domain = $this->applicationConfiguration->getTranslationDomain();
        }

        return $this->translator->trans($id, $parameters, $domain, $locale);
    }

    public function translateKey(string $key, string $adminName = 'ui'): string
    {
        return $this->translate($this->getTranslationKey($key, $adminName));
    }

    public function getTranslationDomain(): string
    {
        return $this->applicationConfiguration->getTranslationDomain();
    }

    /** @deprecated  */
    public function transWithPattern(
        string $id,
        array $parameters = [],
        string $adminName = null,
        string $domain = null,
        string $locale = null,
        string $pattern = null,
    ): string {
        if ($pattern === null) {
            $pattern = $this->applicationConfiguration->getTranslationPattern();
        }

        if ($domain === null) {
            $domain = $this->applicationConfiguration->getTranslationDomain();
        }

        if ($adminName === null) {
            $adminName = $this->adminHelper->getAdmin()->getName();
        }
        $id = self::getTranslationKey(
            $pattern,
            $adminName,
            $id
        );

        return $this->translate($id, [], $domain, $locale);
    }
}
