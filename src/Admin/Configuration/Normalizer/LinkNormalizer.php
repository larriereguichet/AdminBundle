<?php

namespace LAG\AdminBundle\Admin\Configuration\Normalizer;

use LAG\AdminBundle\Application\Configuration\ActionLinkConfiguration;
use Symfony\Component\OptionsResolver\Options;

class LinkNormalizer
{
    public static function normalizeAdminLinks(Options $options, $value): array
    {
        foreach ($value as $actionName => $actionConfiguration) {
            $configuration = new ActionLinkConfiguration();
            $actionConfiguration['admin'] = $actionConfiguration['admin'] ?? $options->offsetGet('name');
            $actionConfiguration['action'] =$actionConfiguration['action'] ??  $actionName;

            $value[$actionName] = $configuration->configure($actionConfiguration)->toArray();
        }

        return $value;
    }

    public static function normalizeActionLinks(Options $options, $value): array
    {
        foreach ($value as $actionName => $actionConfiguration) {
            $configuration = new ActionLinkConfiguration();
            $actionConfiguration['admin'] = $actionConfiguration['admin'] ?? $options->offsetGet('admin_name');
            $actionConfiguration['action'] = $actionConfiguration['action'] ??  $actionName;

            $value[$actionName] = $configuration->configure($actionConfiguration)->toArray();
        }

        return $value;
    }
}
