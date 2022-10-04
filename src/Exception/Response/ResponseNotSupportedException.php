<?php

namespace LAG\AdminBundle\Exception\Response;

use LAG\AdminBundle\Exception\Exception;
use LAG\AdminBundle\Metadata\OperationInterface;

class ResponseNotSupportedException extends Exception
{
    public function __construct(
        OperationInterface $operation,
        mixed $data,
    ) {
        parent::__construct(sprintf(
            'The operation "%s" with data type "%s"s is not supported by any response handler',
            $operation->getName(),
            is_object($data) ? get_class($data) : gettype($data),
        ));
    }
}
