<?php

namespace LAG\AdminBundle\Tests\Field;

use LAG\AdminBundle\Configuration\ApplicationConfiguration;
use LAG\AdminBundle\Factory\FieldFactory;
use LAG\AdminBundle\Factory\FieldFactoryInterface;
use LAG\AdminBundle\Field\FieldInterface;
use LAG\AdminBundle\Tests\TestCase;
use Symfony\Component\EventDispatcher\EventDispatcher;

class FieldTestCase extends TestCase
{
    protected EventDispatcher $eventDispatcher;
    protected FieldFactoryInterface $factory;
    protected ApplicationConfiguration $applicationConfiguration;

    protected function createField(string $name, array $configuration, array $context = []): FieldInterface
    {
        return $this->factory->create($name, $configuration, $context);
    }

    protected function setUp(): void
    {
        $this->eventDispatcher = new EventDispatcher();
        $this->applicationConfiguration = new ApplicationConfiguration(['resources_path' => 'test']);
        $this->factory = new FieldFactory(
            $this->eventDispatcher,
            $this->applicationConfiguration,
            ApplicationConfiguration::FIELD_MAPPING
        );
    }
}
