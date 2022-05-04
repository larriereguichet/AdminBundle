<?php

declare(strict_types=1);

namespace LAG\AdminBundle\Translation\Helper;

/** @deprecated  */
interface TranslationHelperInterface
{
    /**
     * Return the translation pattern with keys "{admin}" and "{key}" replaced by their values.
     */
    public function getTranslationKey(string $key, string $adminName = 'ui'): string;

    public function translate(string $id, array $parameters = [], string $domain = null, string $locale = null): string;

    public function translateKey(string $key, string $adminName = 'ui'): string;

    public function getTranslationDomain(): string;

    /** @deprecated  */
    public function transWithPattern(
        string $id,
        array $parameters = [],
        string $adminName = null,
        string $domain = null,
        string $locale = null,
        string $pattern = null,
    ): string;
}
