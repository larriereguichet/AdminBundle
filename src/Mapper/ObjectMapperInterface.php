<?php

declare(strict_types=1);

namespace LAG\AdminBundle\Mapper;

interface ObjectMapperInterface
{
    /**
     * @param object|class-string $target
     */
    public function map(object $source, object|string $target): object;
}
