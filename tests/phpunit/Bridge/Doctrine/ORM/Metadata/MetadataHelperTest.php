<?php

declare(strict_types=1);

namespace LAG\AdminBundle\Tests\Bridge\Doctrine\ORM\Metadata;

use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\Mapping\ClassMetadataFactory;
use Exception;
use LAG\AdminBundle\Bridge\Doctrine\ORM\Metadata\MetadataHelper;
use LAG\AdminBundle\Tests\TestCase;
use PHPUnit\Framework\MockObject\MockObject;

class MetadataHelperTest extends TestCase
{
    public function testFindMetadata(): void
    {
        [$helper, $entityManager] = $this->createHelper();

        $metadataFactory = $this->createMock(ClassMetadataFactory::class);
        $metadataFactory
            ->expects($this->once())
            ->method('getMetadataFor')
            ->willReturnCallback(function (string $class) {
                $this->assertEquals('MyLittleClass', $class);

                throw new Exception();
            })
        ;

        $entityManager
            ->expects($this->once())
            ->method('getMetadataFactory')
            ->willReturn($metadataFactory)
        ;

        $helper->findMetadata('MyLittleClass');
    }

    public function testService(): void
    {
        $this->assertServiceExists(MetadataHelper::class);
    }

    /**
     * @return MetadataHelper[]|MockObject[]
     */
    private function createHelper(): array
    {
        $entityManager = $this->createMock(EntityManagerInterface::class);

        $helper = new MetadataHelper($entityManager);

        return [
            $helper,
            $entityManager,
        ];
    }
}
