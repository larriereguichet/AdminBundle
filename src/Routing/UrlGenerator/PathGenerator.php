<?php

declare(strict_types=1);

namespace LAG\AdminBundle\Routing\UrlGenerator;

use LAG\AdminBundle\Resource\Metadata\OperationInterface;
use function Symfony\Component\String\u;

final readonly class PathGenerator implements PathGeneratorInterface
{
    public function generatePath(OperationInterface $operation): string
    {
        return u()
            ->append($operation->getPath())
            ->ensureStart('/')
            ->trimEnd('/')
            ->toString()
        ;
    }
}
