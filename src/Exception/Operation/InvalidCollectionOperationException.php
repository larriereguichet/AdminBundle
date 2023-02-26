<?php

declare(strict_types=1);

namespace LAG\AdminBundle\Exception\Operation;

use LAG\AdminBundle\Controller\Index;
use LAG\AdminBundle\Exception\Exception;
use LAG\AdminBundle\Metadata\CollectionOperationInterface;
use LAG\AdminBundle\Metadata\OperationInterface;

class InvalidCollectionOperationException extends Exception
{
    public function __construct(OperationInterface $operation)
    {
        parent::__construct(sprintf(
            'The operation "%s" of the resource "%s" is configured to use the "%s" controller but is not an instance of "%s"',
            $operation->getName(),
            $operation->getResource()->getName(),
            Index::class,
            CollectionOperationInterface::class,
        ));
    }
}
