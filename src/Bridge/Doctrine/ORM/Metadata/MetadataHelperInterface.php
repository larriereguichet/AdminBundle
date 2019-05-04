<?php

namespace LAG\AdminBundle\Bridge\Doctrine\ORM\Metadata;

interface MetadataHelperInterface
{
    public function getFields(string $entityClass): array;
}
