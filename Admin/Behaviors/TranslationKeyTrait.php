<?php

namespace LAG\AdminBundle\Admin\Behaviors;

trait TranslationKeyTrait
{
    /**
     * Return a key used for translation. {admin} token will replaced if provided.
     * 
     * @param $pattern
     * @param $key
     * @param string $adminName
     * @return string
     */
    public function getTranslationKey($pattern, $key, $adminName = null)
    {        
        $translationKey = str_replace('{key}', $key, $pattern);

        if (strstr($pattern, '{admin}') && $adminName !== null) {
            $translationKey = str_replace('{admin}', $adminName, $translationKey);
        }

        return $translationKey;
    }
}
