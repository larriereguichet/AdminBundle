<?php

namespace LAG\AdminBundle\Translation\Helper;

interface TranslationHelperInterface
{
    public function trans(string $id, array $parameters = [], string $domain = null, string $locale = null): string;

    public function transWithPattern(string $message, string $pattern, string $adminName, string $catalog): string;
}
