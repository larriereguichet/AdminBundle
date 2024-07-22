<?php

declare(strict_types=1);

namespace LAG\AdminBundle\Routing\UrlGenerator;

use LAG\AdminBundle\Resource\Metadata\OperationInterface;

interface PathGeneratorInterface
{
    /**
     * Generate the path for the given operation. The operation should be linked to a ressource. The route parameters
     * should be mapped to the property of the given data object when .
     */
    public function generatePath(OperationInterface $operation): string;
}
