<?php

declare(strict_types=1);

namespace LAG\AdminBundle\Bridge\Doctrine\ORM\Exception;

use LAG\AdminBundle\Exception\Exception;
use LAG\AdminBundle\Resource\Metadata\OperationInterface;

class ManagerNotFoundException extends Exception
{
    public function __construct(OperationInterface $resource)
    {
        parent::__construct(sprintf(
            'The data class "%s" of the admin resource "%s" is not managed by any Doctrine entity manager',
            $resource->getResource()->getDataClass(),
            $resource->getResource()->getName(),
        ));
    }
}
