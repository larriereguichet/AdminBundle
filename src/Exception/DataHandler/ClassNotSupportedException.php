<?php

declare(strict_types=1);

namespace LAG\AdminBundle\Exception\DataHandler;

use Exception;
use LAG\AdminBundle\DataProvider\DataSourceInterface;

class ClassNotSupportedException extends Exception
{
    public function __construct(DataSourceInterface $dataSource)
    {
        parent::__construct(sprintf(
            'The data of type "%s" is not supported by any data handlers',
            \get_class($dataSource->getData())
        ));
    }
}
