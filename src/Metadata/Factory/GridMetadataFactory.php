<?php

declare(strict_types=1);

namespace LAG\AdminBundle\Metadata\Factory;

use LAG\AdminBundle\Exception\MissingMetadataException;
use LAG\AdminBundle\Metadata\GridMetadataInterface;

final readonly class GridMetadataFactory implements GridMetadataFactoryInterface
{
    /** @param array<string, string> $gridTemplates */
    public function __construct(
        private GridCollectionMetadataFactoryInterface $metadataFactory,
        private array $gridTemplates,
    ) {
    }

    public function createMetadata(string $gridName): GridMetadataInterface
    {
        $grids = $this->metadataFactory->createMetadata();
        $grid = $grids[$gridName] ?? null;

        if ($grid === null) {
            throw new MissingMetadataException('Unable to find metadata for the grid "%s"', $gridName);
        }
        $type = $grid->getType() ?? 'table';
        $template = $grid->getTemplate() ?? $this->gridTemplates[$type] ?? null;

        return $grid
            ->withType($type)
            ->withTemplate($template)
        ;
    }
}
