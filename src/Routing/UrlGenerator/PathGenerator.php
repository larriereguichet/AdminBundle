<?php

declare(strict_types=1);

namespace LAG\AdminBundle\Routing\UrlGenerator;

use LAG\AdminBundle\Metadata\OperationInterface;

use function Symfony\Component\String\u;

final readonly class PathGenerator implements PathGeneratorInterface
{
    public function generatePath(OperationInterface $operation): string
    {
        return (string) u()
            ->append($operation->getPath())
            ->ensureStart('/')
            ->trimEnd('/')
        ;
    }

    public function generateEmbeddedPath(OperationInterface $operation): string
    {
        return (string) u($this->generatePath($operation))->prepend('/_lag_admin_embedded/');
    }
}
