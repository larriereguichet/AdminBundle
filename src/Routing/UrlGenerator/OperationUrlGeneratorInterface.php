<?php

declare(strict_types=1);

namespace LAG\AdminBundle\Routing\UrlGenerator;

use LAG\AdminBundle\Metadata\OperationInterface;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

interface OperationUrlGeneratorInterface
{
    public function generateUrl(OperationInterface $operation, mixed $data = null, int $referenceType = UrlGeneratorInterface::ABSOLUTE_PATH): string;
}
