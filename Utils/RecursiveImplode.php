<?php

namespace LAG\AdminBundle\Utils;

trait RecursiveImplode
{
    /**
     * Return a imploded string from a multi dimensional array.
     *
     * @param $glue
     * @param array $array
     *
     * @return string
     */
    protected function recursiveImplode($glue, array $array)
    {
        $return = '';
        $index = 0;
        $count = count($array);

        foreach ($array as $piece) {
            if (is_array($piece)) {
                $return .= $this->recursiveImplode($glue, $piece);
            } else {
                $return .= $piece;
            }
            if ($index < $count - 1) {
                $return .=  $glue;
            }
            ++$index;
        }

        return $return;
    }
}
