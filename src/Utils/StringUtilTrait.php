<?php

namespace LAG\AdminBundle\Utils;

trait StringUtilTrait
{
    /**
     * Camelizes a string.
     *
     * @param string $id A string to camelize
     *
     * @return string The camelized string
     */
    public function camelize($id): string
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
    public function underscore($id): string
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

    public function startWith($string, $start): bool
    {
        return substr($string, 0, strlen($start)) === $start;
    }
}
