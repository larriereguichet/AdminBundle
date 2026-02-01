<?php

declare(strict_types=1);

namespace LAG\AdminBundle\Metadata\Factory;

use LAG\AdminBundle\Config\ConfigurationMapper;
use LAG\AdminBundle\Exception\MissingMetadataException;
use LAG\AdminBundle\Metadata\GridInterface;

final readonly class GridMetadataFactory implements GridMetadataFactoryInterface
{
    public function __construct(
        /** @var  array<string, array<string, mixed>> $grids */
        private array $grids,
        private ConfigurationMapper $configurationMapper = new ConfigurationMapper(),
    ) {
    }

    public function create(string $gridName): GridInterface
    {
        if (!\array_key_exists($gridName, $this->grids)) {
            throw new MissingMetadataException('Unable to find metadata fir the grid "%s"', $gridName);
        }

        return $this->configurationMapper->toGrid($this->grids[$gridName]);
    }
}
