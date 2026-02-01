<?php

declare(strict_types=1);

namespace LAG\AdminBundle\Resource\Factory;

use LAG\AdminBundle\Metadata\Attribute\Application;

final readonly class ApplicationFactory implements ApplicationFactoryInterface
{
    public function __construct(
        private DefinitionFactoryInterface $definitionFactory,
    ) {
    }

    public function create(string $applicationName): Application
    {
        // TODO validate definition
        return $this->definitionFactory->createApplicationDefinition($applicationName);
    }
}
