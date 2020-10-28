<?php

namespace LAG\AdminBundle\Factory;

use LAG\AdminBundle\Exception\Exception;
use LAG\AdminBundle\Field\Definition\FieldDefinitionInterface;
use LAG\AdminBundle\Field\FieldInterface;

/**
 * Field factory. Instances fields.
 */
interface FieldFactoryInterface
{
    /**
     * Create a new field instance according to the given configuration. A optional context can be passed and will be
     * added when dispatching field events.
     *
     * @throws Exception
     */
    public function create(string $name, array $configuration, array $context = []): FieldInterface;

    /**
     * Return the definition of the field.
     *
     * @return FieldDefinitionInterface[]
     */
    public function createDefinitions(string $class): array;
}
