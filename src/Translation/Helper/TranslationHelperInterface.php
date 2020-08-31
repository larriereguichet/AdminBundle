<?php

namespace LAG\AdminBundle\Translation\Helper;

use LAG\AdminBundle\Configuration\AdminConfiguration;

interface TranslationHelperInterface
{
    public function trans(string $id, array $parameters = [], string $domain = null, string $locale = null): string;

    public function transWithPattern(AdminConfiguration $configuration, string $id): string;
}
