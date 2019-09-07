<?php

namespace LAG\AdminBundle\Utils;

class TranslationUtils
{
    /**
     * Return the translation pattern with keys "{admin}" and "{key}" replaced by their values.
     *
     * @param string $translationPattern
     * @param string $adminName
     * @param string $key
     *
     * @return string
     */
    public static function getTranslationKey(
        string $translationPattern,
        string $adminName,
        string $key
    ): string {
        $translationPattern = str_replace('{key}', $key, $translationPattern);
        $translationPattern = str_replace('{admin}', $adminName, $translationPattern);

        return $translationPattern;
    }

    /**
     * Return the translation pattern with keys "{admin}" and "{key}" replaced by their values.
     *
     * @param string $translationPattern
     * @param string $adminName
     * @param string $actionName
     *
     * @return string
     */
    public static function getActionTranslationKey(
        string $translationPattern,
        string $adminName,
        string $actionName
    ): string {
        $translationPattern = str_replace('{key}', $actionName, $translationPattern);
        $translationPattern = str_replace('{admin}', $adminName, $translationPattern);

        return $translationPattern;
    }
}