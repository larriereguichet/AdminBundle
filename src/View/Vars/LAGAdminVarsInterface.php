<?php

declare(strict_types=1);

namespace LAG\AdminBundle\View\Vars;

use LAG\AdminBundle\Resource\Metadata\Application;
use LAG\AdminBundle\Resource\Metadata\OperationInterface;
use LAG\AdminBundle\Resource\Metadata\Resource;

interface LAGAdminVarsInterface
{
    public function getApplication(): ?Application;

    public function getResource(): ?Resource;

    public function getOperation(): ?OperationInterface;
}
