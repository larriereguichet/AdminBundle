<?php

declare(strict_types=1);

namespace LAG\AdminBundle\Grid\Provider;

use LAG\AdminBundle\Metadata\GridMetadataInterface;

/**
 * Build a single grid to be used in one or several resource collection view.
 */
interface GridProviderInterface
{
    public function supports(string $gridName): bool;

    public function provide(string $gridName): GridMetadataInterface;
}
