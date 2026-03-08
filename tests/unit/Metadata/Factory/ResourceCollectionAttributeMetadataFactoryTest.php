<?php

declare(strict_types=1);

namespace LAG\AdminBundle\Tests\Unit\Metadata\Factory;

use LAG\AdminBundle\Metadata\Attribute\Create;
use LAG\AdminBundle\Metadata\Attribute\Delete;
use LAG\AdminBundle\Metadata\Attribute\Index;
use LAG\AdminBundle\Metadata\Attribute\Resource;
use LAG\AdminBundle\Metadata\Attribute\Show;
use LAG\AdminBundle\Metadata\Attribute\Update;
use LAG\AdminBundle\Metadata\Factory\ResourceCollectionAttributeMetadataFactory;
use LAG\AdminBundle\Metadata\Factory\ResourceCollectionMetadataFactoryInterface;
use LAG\AdminBundle\Tests\Application\Entity\Author;
use LAG\AdminBundle\Tests\Application\Entity\Book;
use LAG\AdminBundle\Tests\Application\State\Provider\Book\LatestBookProvider;
use LAG\AdminBundle\Tests\Unit\ApplicationTestTrait;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

final class ResourceCollectionAttributeMetadataFactoryTest extends TestCase
{
    use ApplicationTestTrait;

    private ResourceCollectionAttributeMetadataFactory $metadataFactory;
    private MockObject $decorated;

    #[Test]
    public function itCreatesMetadataFromAttributes(): void
    {
        $this->decorated
            ->expects($this->once())
            ->method('createMetadata')
            ->willReturn([])
        ;
        $resources = $this->metadataFactory->createMetadata();

        self::assertCount(2, $resources);
        self::assertEquals(new Resource(
            shortName: 'book',
            resourceClass: Book::class,
            pathPrefix: '/books',
            operations: [
                new Index(grid: 'projects_table'),
                new Show(),
                new Show(
                    name: 'latest',
                    path: '/latest',
                    provider: LatestBookProvider::class
                ),
            ],
        ), $resources['admin.book']);
        self::assertEquals(new Resource(
            shortName: 'author',
            resourceClass: Author::class,
            operations: [
                new Index(grid: 'authors'),
                new Create(),
                new Update(),
                new Delete(),
                new Show(),
            ],
        ), $resources['admin.author']);
    }

    protected function setUp(): void
    {
        $this->decorated = $this->createMock(ResourceCollectionMetadataFactoryInterface::class);
        $this->metadataFactory = new ResourceCollectionAttributeMetadataFactory(
            $this->decorated,
            [$this->getTestApplicationPath().'/src/Entity'],
        );
    }
}
