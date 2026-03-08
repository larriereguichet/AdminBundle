<?php

declare(strict_types=1);

namespace LAG\AdminBundle\Metadata\Factory;

use LAG\AdminBundle\Config\ConfigurationMapper;
use LAG\AdminBundle\Exception\Resource\MissingApplicationException;
use LAG\AdminBundle\Metadata\ApplicationMetadataInterface;

final readonly class ApplicationMetadataFactory implements ApplicationMetadataFactoryInterface
{
    /**
     * @param array<string, array<string, mixed>> $applications
     */
    public function __construct(
        private array $applications,
        private ConfigurationMapper $configurationMapper = new ConfigurationMapper(),
    ) {
    }

    public function createMetadata(string $applicationName): ApplicationMetadataInterface
    {
        if (!\array_key_exists($applicationName, $this->applications)) {
            throw new MissingApplicationException($applicationName);
        }

        return $this->configurationMapper->toApplication($this->applications[$applicationName]);
    }
}
