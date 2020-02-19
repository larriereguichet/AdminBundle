<?php

namespace LAG\AdminBundle\Routing\Resolver;

use LAG\AdminBundle\Configuration\ApplicationConfiguration;
use LAG\AdminBundle\Configuration\ApplicationConfigurationStorage;
use LAG\AdminBundle\Exception\Exception;

class RoutingResolver implements RoutingResolverInterface
{
    /**
     * @var ApplicationConfiguration
     */
    private $configuration;

    public function __construct(ApplicationConfigurationStorage $storage)
    {
        $this->configuration = $storage->getConfiguration();
    }

    public function resolve(string $adminName, string $actionName): string
    {
        $routeName = str_replace(
            '{admin}',
            strtolower($adminName),
            $this->configuration->get('routing_name_pattern')
        );

        $routeName = str_replace(
            '{action}',
            $actionName,
            $routeName
        );

        return $routeName;
    }

    public function resolveOptions(array $options): ?string
    {
        if (key_exists('route', $options) && $options['route']) {
            return $options['route'];
        }

        if ((key_exists('uri', $options) && $options['uri']) || (key_exists('url', $options) && $options['url'])) {
            return null;
        }

        if (!key_exists('admin', $options)) {
            throw new Exception('Cannot resolve options, missing "admin" key');
        }

        if (!key_exists('action', $options)) {
            throw new Exception('Cannot resolve options, missing "action" key');
        }

        return $this->resolve($options['admin'], $options['action']);
    }
}
