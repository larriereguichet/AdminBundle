<?php

declare(strict_types=1);

namespace LAG\AdminBundle\Tests\Bridge\Doctrine\ORM\Metadata;

use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Mapping\ClassMetadataFactory;
use LAG\AdminBundle\Bridge\Doctrine\ORM\Metadata\MetadataHelper;
use LAG\AdminBundle\Bridge\Doctrine\ORM\Metadata\MetadataHelperInterface;
use LAG\AdminBundle\Tests\ContainerTestTrait;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

final class MetadataHelperTest extends TestCase
{
    use ContainerTestTrait;

    private MetadataHelperInterface $helper;
    private MockObject $entityManager;

    #[Test]
    public function itFindMetadata(): void
    {
        $metadataFactory = self::createMock(ClassMetadataFactory::class);
        $metadataFactory
            ->expects(self::once())
            ->method('getMetadataFor')
            ->willReturnCallback(function (string $class) {
                $this->assertEquals('MyLittleClass', $class);

                throw new \Exception();
            })
        ;

        $this->entityManager
            ->expects(self::once())
            ->method('getMetadataFactory')
            ->willReturn($metadataFactory)
        ;

        $this->helper->findMetadata('MyLittleClass');
    }

    protected function setUp(): void
    {
        $this->entityManager = self::createMock(EntityManagerInterface::class);
        $this->helper = new MetadataHelper($this->entityManager);
    }
}
