<?php

declare(strict_types=1);

namespace LAG\AdminBundle\Tests\Unit\EventListener\Data;

use LAG\AdminBundle\Event\DataEvent;
use LAG\AdminBundle\EventListener\Data\GenerateTimestampListener;
use LAG\AdminBundle\Metadata\OperationInterface;
use LAG\AdminBundle\Tests\Unit\DataProviderTestTrait;
use LAG\AdminBundle\Tests\Unit\Fixtures\Author;
use LAG\AdminBundle\Tests\Unit\Fixtures\Book;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

final class GenerateTimestampListenerTest extends TestCase
{
    use DataProviderTestTrait;

    private GenerateTimestampListener $listener;

    #[Test]
    #[DataProvider('operations')]
    public function itGenerateTimestamp(OperationInterface $operation): void
    {
        $data = new Book();
        $event = new DataEvent($data, $operation);

        $this->listener->__invoke($event);

        $this->assertNotNull($data->getCreatedAt());
        $this->assertNotNull($data->getUpdatedAt());
    }

    #[Test]
    #[DataProvider('operations')]
    public function itDoesNotSetCreatedAtTwice(OperationInterface $operation): void
    {
        $data = new Book();
        $event = new DataEvent($data, $operation);

        $this->listener->__invoke($event);

        $this->assertNotNull($data->getCreatedAt());
        $this->assertNotNull($data->getUpdatedAt());

        $createdAt = $data->getCreatedAt();
        $updatedAt = $data->getUpdatedAt();

        $this->listener->__invoke($event);

        $this->assertEquals($createdAt, $data->getCreatedAt());
        $this->assertNotEquals($updatedAt, $data->getUpdatedAt());
    }

    #[Test]
    #[DataProvider('operations')]
    public function itDoesNotGenerateTimestampOnInvalidResource(OperationInterface $operation): void
    {
        $data = new Author();
        $event = new DataEvent($data, $operation);

        $this->listener->__invoke($event);

        $this->assertNull($data->getCreatedAt());
        $this->assertNull($data->getUpdatedAt());
    }

    protected function setUp(): void
    {
        $this->listener = new GenerateTimestampListener();
    }
}
