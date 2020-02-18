<?php

namespace LAG\AdminBundle\Tests\Bridge\Doctrine\ORM\Metadata;

use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Mapping\ClassMetadataInfo;
use Doctrine\Persistence\Mapping\ClassMetadataFactory;
use Exception;
use LAG\AdminBundle\Bridge\Doctrine\ORM\Metadata\MetadataHelper;
use LAG\AdminBundle\Tests\AdminTestBase;
use PHPUnit\Framework\MockObject\MockObject;
use stdClass;

class MetadataHelperTest extends AdminTestBase
{
    public function testGetFields()
    {
        list($helper, $entityManager) = $this->createHelper();

        $fields = [
            'id' => [],
            'name' => [],
        ];

        $metadata = $this->createMock(ClassMetadataInfo::class);
        $metadata->fieldNames = array_keys($fields);
        $metadata->identifier = ['id'];
        $metadata->associationMappings = [
            'things' => [
                'type' => ClassMetadataInfo::MANY_TO_MANY,
                'joinColumns' => [
                    'nullable' => false,
                ],
            ],
            'pandas' => [
                'type' => ClassMetadataInfo::MANY_TO_MANY,
            ],
            'bamboos' => [
                'type' => ClassMetadataInfo::MANY_TO_MANY,
                'joinColumns' => [],
            ]
        ];
        $metadata
            ->expects($this->atLeastOnce())
            ->method('isIdentifierNatural')
            ->willReturn(false)
        ;
        $metadata
            ->expects($this->atLeastOnce())
            ->method('getFieldMapping')
            ->willReturnCallback(function (string $field) use ($fields) {
                $this->assertArrayHasKey($field, $fields);

                return [
                    'nullable' => true,
                ];
            })
        ;
        $metadata
            ->expects($this->atLeastOnce())
            ->method('getTypeOfField')
            ->willReturnCallback(function (string $field) use ($fields) {
                $this->assertArrayHasKey($field, $fields);

                return 'string';
            })
        ;

        $entityManager
            ->expects($this->once())
            ->method('getClassMetadata')
            ->with('MyLittleClass')
            ->willReturn($metadata)
        ;

        $helper->getFields('MyLittleClass');
    }

    public function testGetFieldsWithInvalidMetadata()
    {
        list($helper, $entityManager) = $this->createHelper();

        $entityManager
            ->expects($this->once())
            ->method('getClassMetadata')
            ->with('MyLittleClass')
            ->willReturn(new stdClass())
        ;
        $data = $helper->getFields('MyLittleClass');

        $this->assertEquals([], $data);
    }

    public function testMetadata()
    {
        list($helper, $entityManager) = $this->createHelper();

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
