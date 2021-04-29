<?php

namespace LAG\AdminBundle\Translation\Helper;

interface TranslationHelperInterface
{
    public function trans(string $id, array $parameters = [], string $domain = null, string $locale = null): string;

    public function transWithPattern(
        string $id,
        array $parameters = [],
        string $domain = null,
        string $locale = null,
        string $pattern = null,
        string $adminName = null
    ): string;
}
