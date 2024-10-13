<?php

declare(strict_types=1);

namespace LAG\AdminBundle\Tests\EventListener\Resource;

use LAG\AdminBundle\Event\ResourceEvent;
use LAG\AdminBundle\EventListener\Resource\InitializeResourceListener;
use LAG\AdminBundle\Resource\Metadata\Resource;
use LAG\AdminBundle\Resource\Registry\ApplicationRegistryInterface;
use LAG\AdminBundle\Tests\TestCase;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\MockObject\MockObject;

final class InitializeResourceListenerTest extends TestCase
{
    private InitializeResourceListener $listener;
    private MockObject $applicationRegistry;

    #[Test]
    public function itDefinesApplication(): void
    {
        $resource = new Resource(name: 'some_resource');
        $event = new ResourceEvent($resource);

        $this->listener->__invoke($event);

        self::assertEquals('some_application', $event->getResource()->getApplication());
    }

    #[Test]
    public function itDefinesTitle(): void
    {
        $resource = new Resource(name: 'some_resource');
        $event = new ResourceEvent($resource);

        $this->listener->__invoke($event);

        self::assertEquals('Some resources', $event->getResource()->getTitle());
    }

    #[Test]
    public function itDefinesTranslationDomain(): void
    {
        $resource = new Resource(name: 'some_resource');
        $event = new ResourceEvent($resource);

        $this->listener->__invoke($event);

        self::assertEquals('some_translation_domain', $event->getResource()->getTranslationDomain());
    }

    #[Test]
    public function itDefinesTranslationPattern(): void
    {
        $resource = new Resource(name: 'some_resource');
        $event = new ResourceEvent($resource);

        $this->listener->__invoke($event);

        self::assertEquals('{application}.{resource}.{message}', $event->getResource()->getTranslationPattern());
    }

    #[Test]
    public function itDefinesRoutesPattern(): void
    {
        $resource = new Resource(name: 'some_resource');
        $event = new ResourceEvent($resource);

        $this->listener->__invoke($event);

        self::assertEquals('{application}.{resource}.{operation}', $event->getResource()->getRoutePattern());
    }

    #[Test]
    public function itDefinesPermissions(): void
    {
        $resource = new Resource(name: 'some_resource', permissions: ['ROLE_ADMIN']);
        $event = new ResourceEvent($resource);

        $this->listener->__invoke($event);

        self::assertEquals(['ROLE_ADMIN'], $event->getResource()->getPermissions());
    }

    #[Test]
    public function itDefinesNormalizationContext(): void
    {
        $resource = new Resource(name: 'some_resource');
        $event = new ResourceEvent($resource);

        $this->listener->__invoke($event);

        self::assertEquals([], $event->getResource()->getNormalizationContext());
    }

    #[Test]
    public function itDefinesDenormalizationContext(): void
    {
        $resource = new Resource(name: 'some_resource');
        $event = new ResourceEvent($resource);

        $this->listener->__invoke($event);

        self::assertEquals([], $event->getResource()->getDenormalizationContext());
    }

    protected function setUp(): void
    {
        $this->applicationRegistry = self::createMock(ApplicationRegistryInterface::class);
        $this->listener = new InitializeResourceListener(
            'some_application',
            'some_translation_domain',
            $this->applicationRegistry
        );
    }
}
