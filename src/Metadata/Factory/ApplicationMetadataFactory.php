<?php

declare(strict_types=1);

namespace LAG\AdminBundle\Metadata\Factory;

use LAG\AdminBundle\Exception\Resource\MissingApplicationException;
use LAG\AdminBundle\Metadata\ApplicationMetadataInterface;
use LAG\AdminBundle\Metadata\Attribute\Application;

final readonly class ApplicationMetadataFactory implements ApplicationMetadataFactoryInterface
{
    /**
     * @param array<string, array<string, mixed>> $applications
     */
    public function __construct(
        private array $applications,
    ) {
    }

    public function createMetadata(string $applicationName): ApplicationMetadataInterface
    {
        if (!\array_key_exists($applicationName, $this->applications)) {
            throw new MissingApplicationException($applicationName);
        }
        $application = $this->applications[$applicationName];

        return new Application(
            name: $application['name'] ?? $applicationName,
            dateFormat: $application['date_format'],
            timeFormat: $application['time_format'],
            translationDomain: $application['translation_domain'],
            translationPattern: $application['translation_pattern'],
            routePattern: $application['route_pattern'],
            baseTemplate: $application['base_template'],
        );
    }
}
