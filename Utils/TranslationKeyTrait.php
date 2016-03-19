<?php

namespace LAG\AdminBundle\Utils;

use LAG\AdminBundle\Application\Configuration\ApplicationConfiguration;

trait TranslationKeyTrait
{
    /**
     * @param ApplicationConfiguration $configuration
     * @param $key
     * @param string $adminName
     * @return string
     */
    public function getTranslationKey(ApplicationConfiguration $configuration, $key, $adminName = null)
    {
        $translationKey = $configuration->getParameter('translation')['pattern'];

        if (strstr($configuration->getParameter('translation')['pattern'], '{admin}') && $adminName != null) {
            $translationKey = str_replace('{admin}', $adminName, $translationKey);
        }
        $translationKey = str_replace('{key}', $key, $translationKey);

        return $translationKey;
    }
}
