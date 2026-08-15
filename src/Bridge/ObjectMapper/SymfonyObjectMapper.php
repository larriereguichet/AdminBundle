<?php

declare(strict_types=1);

namespace LAG\AdminBundle\Bridge\ObjectMapper;

use LAG\AdminBundle\Mapper\ObjectMapperInterface;
use Symfony\Component\ObjectMapper\ObjectMapperInterface as SymfonyObjectMapperInterface;

/**
 * Adapts the Symfony object mapper to the bundle mapper interface, so the state providers and
 * processors do not depend on the optional symfony/object-mapper component.
 */
final readonly class SymfonyObjectMapper implements ObjectMapperInterface
{
    public function __construct(
        private SymfonyObjectMapperInterface $objectMapper,
    ) {
    }

    public function map(object $source, object|string $target): object
    {
        return $this->objectMapper->map($source, $target);
    }
}
