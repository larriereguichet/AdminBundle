<?php

declare(strict_types=1);

namespace LAG\AdminBundle\Tests\Unit\EventListener\Data;

use LAG\AdminBundle\Event\DataEvent;
use LAG\AdminBundle\EventListener\Data\GenerateSlugListener;
use LAG\AdminBundle\Metadata\Attribute\Resource;
use LAG\AdminBundle\Metadata\Attribute\Slug;
use LAG\AdminBundle\Metadata\OperationInterface;
use LAG\AdminBundle\Resource\Slug\ResourceSluggerInterface;
use LAG\AdminBundle\Tests\Unit\DataProviderTestTrait;
use LAG\AdminBundle\Tests\Unit\Fixtures\Book;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

final class GenerateSlugListenerTest extends TestCase
{
    use DataProviderTestTrait;

    private GenerateSlugListener $listener;
    private MockObject $slugger;

    #[Test]
    #[DataProvider('operations')]
    public function itGeneratesASlug(OperationInterface $operation): void
    {
        $data = new Book();
        $data->setName('A beautiful book');

        $operation = $operation->setResource(new Resource()
            ->withProperties([new Slug(propertyPath: 'slug', source: 'name')])
        );

        $event = new DataEvent($data, $operation);

        $this->slugger
            ->expects($this->once())
            ->method('generateSlug')
            ->willReturnCallback(static function () use ($data) {
                $data->setSlug('a-beautiful-slug');

                return 'a-beautiful-slug';
            })
        ;

        $this->listener->__invoke($event);

        $this->assertEquals('a-beautiful-slug', $data->getSlug());
    }

    protected function setUp(): void
    {
        $this->slugger = $this->createMock(ResourceSluggerInterface::class);
        $this->listener = new GenerateSlugListener($this->slugger);
    }
}
