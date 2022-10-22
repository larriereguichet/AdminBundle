<?php

declare(strict_types=1);

namespace LAG\AdminBundle\Bridge\Doctrine\ORM\Metadata;

interface MetadataPropertyFactoryInterface
{
    public function createProperties(string $resourceClass): array;
}
