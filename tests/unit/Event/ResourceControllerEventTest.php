<?php

declare(strict_types=1);

namespace LAG\AdminBundle\Tests\Unit\Event;

use LAG\AdminBundle\Event\ResourceControllerEvent;
use LAG\AdminBundle\Metadata\Attribute\Index;
use LAG\AdminBundle\Metadata\Attribute\Resource;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

final class ResourceControllerEventTest extends TestCase
{
    #[Test]
    public function itReturnsEventData(): void
    {
        $resource = new Resource(shortName: 'my_resource');
        $operation = new Index()->setResource($resource);
        $request = new Request();
        $data = new \stdClass();

        $event = new ResourceControllerEvent($operation, $request, $data);

        self::assertSame($resource, $event->getResource());
        self::assertSame($operation, $event->getOperation());
        self::assertSame($request, $event->getRequest());
        self::assertSame($data, $event->getData());
        self::assertNull($event->getResponse());
    }

    #[Test]
    public function itSetsResponse(): void
    {
        $resource = new Resource(shortName: 'my_resource');
        $operation = new Index()->setResource($resource);
        $event = new ResourceControllerEvent($operation, new Request(), null);

        $response = new Response('body');
        $event->setResponse($response);

        self::assertSame($response, $event->getResponse());
    }
}
