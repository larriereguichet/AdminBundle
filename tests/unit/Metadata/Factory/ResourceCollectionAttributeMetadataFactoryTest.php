<?php

declare(strict_types=1);

namespace LAG\AdminBundle\Tests\Unit\Metadata\Factory;

use LAG\AdminBundle\Exception\Resource\MissingResourceNameException;
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
use PHPUnit\Framework\Attributes\AllowMockObjectsWithoutExpectations;
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

    #[Test]
    #[AllowMockObjectsWithoutExpectations]
    public function itThrowsForResourceWithEmptyShortName(): void
    {
        $tempDir = sys_get_temp_dir().'/lag_admin_test_'.uniqid();
        mkdir($tempDir);
        $className = 'EmptyShortNameResource'.str_replace('.', '', uniqid('', false));
        $tempFile = $tempDir.'/'.$className.'.php';
        file_put_contents(
            $tempFile,
            "<?php\nuse LAG\\AdminBundle\\Metadata\\Attribute\\Resource;\n\n#[Resource(shortName: '')]\nclass $className {}\n",
        );
        require $tempFile;

        $decorated = $this->createStub(ResourceCollectionMetadataFactoryInterface::class);
        $decorated->method('createMetadata')->willReturn([]);
        $factory = new ResourceCollectionAttributeMetadataFactory($decorated, [$tempDir]);

        $this->expectException(MissingResourceNameException::class);

        try {
            $factory->createMetadata();
        } finally {
            unlink($tempFile);
            rmdir($tempDir);
        }
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
