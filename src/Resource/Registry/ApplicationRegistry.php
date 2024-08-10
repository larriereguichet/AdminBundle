<?php

declare(strict_types=1);

namespace LAG\AdminBundle\Resource\Registry;

use LAG\AdminBundle\Exception\Exception;
use LAG\AdminBundle\Resource\Metadata\Application;

final readonly class ApplicationRegistry implements ApplicationRegistryInterface
{
    private array $applications;

    public function __construct(
        private array $configurations,
    ) {
        $applications = [];

        foreach ($this->configurations as $name => $configuration) {
            $applications[$name] = new Application(
                name: $name,
                dateFormat: $configuration['date_format'] ?? null,
                timeFormat: $configuration['time_format'] ?? null,
                translationDomain: $configuration['translation_domain'] ?? null,
                translationPattern: $configuration['translation_pattern'] ?? null,
                routePattern: $configuration['route_pattern'] ?? null,
                baseTemplate: $configuration['base_template'] ?? null,
            );
        }
        $this->applications = $applications;
    }

    public function get(string $name): Application
    {
        if (!\array_key_exists($name, $this->applications)) {
            throw new Exception(\sprintf('The application "%s" does not exist.', $name));
        }

        return $this->applications[$name];
    }

    public function has(string $name): bool
    {
        return \array_key_exists($name, $this->applications);
    }
}
