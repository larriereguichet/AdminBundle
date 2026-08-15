<?php

declare(strict_types=1);

namespace LAG\AdminBundle\Tests\Unit\Event;

use LAG\AdminBundle\Event\DataEvent;
use LAG\AdminBundle\Metadata\Attribute\Index;
use LAG\AdminBundle\Metadata\Attribute\Resource;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

final class DataEventTest extends TestCase
{
    #[Test]
    public function itReturnsEventData(): void
    {
        $resource = new Resource(shortName: 'my_resource');
        $operation = new Index()->setResource($resource);
        $data = new \stdClass();

        $event = new DataEvent($data, $operation);

        self::assertSame($data, $event->getData());
        self::assertSame($operation, $event->getOperation());
        self::assertSame($resource, $event->getResource());
    }
}
