<?php

declare(strict_types=1);

namespace LAG\AdminBundle\Resource\Resolver;

interface PhpFileResolverInterface
{
    public function resolveFile(string $path): mixed;
}
