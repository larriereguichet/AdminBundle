<?php

declare(strict_types=1);

namespace LAG\AdminBundle\Resource\Initializer;

use LAG\AdminBundle\Metadata\Application;
use LAG\AdminBundle\Metadata\CollectionOperationInterface;

interface CollectionOperationInitializerInterface
{
    public function initializeCollectionOperation(Application $application, CollectionOperationInterface $operation): CollectionOperationInterface;
}
