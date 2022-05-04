<?php

namespace LAG\AdminBundle\Admin\Configuration\Normalizer;

use LAG\AdminBundle\Exception\Exception;

// TODO remove
class LinkNormalizer
{
    public static function normalize(array $value, ?string $adminName = null, ?string $actionName = null): array
    {
        if (isset($value['route'])) {
            return $value;
        }

        if (isset($value['url'])) {
            return $value;
        }

        if (isset($value['admin']) && isset($value['action'])) {
            return $value;
        }

        if ($adminName !== null && $actionName !== null) {
            $value['admin'] = $adminName;
            $value['action'] = $actionName;

            return $value;
        }

        throw new Exception('The link action should contains an url, a route, or an admin and action');
    }
}
