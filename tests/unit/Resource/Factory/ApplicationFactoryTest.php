<?php

declare(strict_types=1);

namespace LAG\AdminBundle\Tests\Resource\Factory;

use LAG\AdminBundle\Metadata\Application;
use LAG\AdminBundle\Resource\Factory\ApplicationFactory;
use LAG\AdminBundle\Resource\Factory\DefinitionFactoryInterface;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

final class ApplicationFactoryTest extends TestCase
{
    private ApplicationFactory $applicationFactory;
    private MockObject $definitionFactory;

    #[Test]
    public function itCreatesAnApplication(): void
    {
        $applicationDefinition = new Application(name: 'my_application');

        $this->definitionFactory
            ->expects(self::once())
            ->method('createApplicationDefinition')
            ->with('my_application')
            ->willReturn($applicationDefinition)
        ;

        $application = $this->applicationFactory->create('my_application');

        self::assertEquals($applicationDefinition, $application);
    }

    protected function setUp(): void
    {
        $this->definitionFactory = self::createMock(DefinitionFactoryInterface::class);
        $this->applicationFactory = new ApplicationFactory($this->definitionFactory);
    }
}
