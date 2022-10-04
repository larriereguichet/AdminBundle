<?php

namespace LAG\AdminBundle\Bridge\Doctrine\ORM;

use LAG\AdminBundle\Exception\Exception;
use LAG\AdminBundle\Metadata\OperationInterface;

class EntityManagerNotFoundException extends Exception
{
    public function __construct(OperationInterface $operation)
    {
        parent::__construct(sprintf(
            'The data class of the admin resource "%s" is not managed by any Doctrine entity manager',
            $operation->getResourceName(),
        ));
    }
}
