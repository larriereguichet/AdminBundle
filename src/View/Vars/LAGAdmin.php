<?php

declare(strict_types=1);

namespace LAG\AdminBundle\View\Vars;

use LAG\AdminBundle\Resource\Metadata\Application;
use LAG\AdminBundle\Resource\Metadata\OperationInterface;
use LAG\AdminBundle\Resource\Metadata\Resource;

final readonly class LAGAdmin
{
    public function __construct(
        public Application $application,
        public Resource $resource,
        public OperationInterface $operation,
    ) {
    }
}
