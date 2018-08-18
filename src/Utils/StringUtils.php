<?php

namespace LAG\AdminBundle\Utils;

class StringUtils
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
     * Camelize a string.
     *
     * @param string $id A string to camelize
     *
     * @return string The camelized string
     */
    public static function camelize($id): string
    {
        return strtr(ucwords(strtr($id, ['_' => ' ', '.' => '_ ', '\\' => '_ '])), [' ' => '']);
    }

    /**
     * A string to underscore.
     *
     * @param string $id The string to underscore
     *
     * @return string The underscored string
     */
    public static function underscore($id): string
    {
        return strtolower(preg_replace([
                '/([A-Z]+)([A-Z][a-z])/',
                '/([a-z\d])([A-Z])/'
            ], [
                '\\1_\\2',
                '\\1_\\2'
            ],
                str_replace('_', '.', $id))
        );
    }

    /**
     * Return true if the given string starts with $start.
     *
     * @param string $string
     * @param string $start
     *
     * @return bool
     */
    public static function startWith(string $string, string $start): bool
    {
        return substr($string, 0, strlen($start)) === $start;
    }
}
