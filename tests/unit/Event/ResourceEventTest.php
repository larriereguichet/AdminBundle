<?php

declare(strict_types=1);

namespace LAG\AdminBundle\Tests\Unit\Event;

use LAG\AdminBundle\Event\ResourceEvent;
use LAG\AdminBundle\Metadata\Attribute\Resource;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

final class ResourceEventTest extends TestCase
{
    #[Test]
    public function itReturnsTheResource(): void
    {
        $resource = new Resource(shortName: 'my_resource');
        $event = new ResourceEvent($resource);

        self::assertSame($resource, $event->getResource());
    }

    #[Test]
    public function itSetsTheResource(): void
    {
        $resource = new Resource(shortName: 'my_resource');
        $newResource = new Resource(shortName: 'other_resource');
        $event = new ResourceEvent($resource);

        $event->setResource($newResource);

        self::assertSame($newResource, $event->getResource());
    }
}
