<?php

namespace LAG\AdminBundle\Bridge\Doctrine\ORM\Metadata;

interface MetadataPropertyFactoryInterface
{
    public function createProperties(string $resourceClass): array;
}
