<?php

namespace LAG\AdminBundle\Tests\Bridge\Doctrine\Event\Listener;

use Doctrine\ORM\Mapping\ClassMetadataInfo;
use LAG\AdminBundle\Bridge\Doctrine\Event\Listener\CreateFieldDefinitionListener;
use LAG\AdminBundle\Bridge\Doctrine\ORM\Metadata\MetadataHelperInterface;
use LAG\AdminBundle\Event\Events\FieldDefinitionEvent;
use LAG\AdminBundle\Tests\TestCase;
use PHPUnit\Framework\MockObject\MockObject;

class CreateFieldDefinitionListenerTest extends TestCase
{
    private CreateFieldDefinitionListener $listener;
    private MockObject $metadataHelper;

    public function testService(): void
    {
        $this->assertServiceExists(CreateFieldDefinitionListener::class);
    }

    public function testInvoke(): void
    {
        $metadata = new ClassMetadataInfo('my_class');
        $metadata->generatorType = ClassMetadataInfo::GENERATOR_TYPE_AUTO;
        $metadata->fieldNames = [
            'id',
            'name',
        ];
        $metadata->identifier = ['id'];
        $metadata->fieldMappings = [
            'id' => [
                'type' => 'integer',
                'nullable' => false,
            ],
            'name' => [
                'type' => 'string',
                'nullable' => true,
            ],
        ];
        $metadata->associationMappings = [
            'relation_field' => [
                'type' => ClassMetadataInfo::MANY_TO_MANY,
                'joinColumns' => [
                    'nullable' => false,
                ],
            ],
            'relation_field_no_column' => [
                'type' => ClassMetadataInfo::MANY_TO_MANY,
            ],
            'relation_field_no_nullable' => [
                'type' => ClassMetadataInfo::MANY_TO_MANY,
                'joinColumns' => [],
            ],
        ];

        $this
            ->metadataHelper
            ->expects($this->once())
            ->method('findMetadata')
            ->with('my_class')
            ->willReturn($metadata)
        ;
        $event = new FieldDefinitionEvent('my_class');
        $this->listener->__invoke($event);

        $this->assertFalse($event->getDefinitions()['name']->getFormOptions()['required']);
    }

    public function testInvokeWithoutMetadata(): void
    {
        $this
            ->metadataHelper
            ->expects($this->once())
            ->method('findMetadata')
            ->with('my_class')
            ->willReturn(null)
        ;
        $this->listener->__invoke(new FieldDefinitionEvent('my_class'));
    }

    protected function setUp(): void
    {
        $this->metadataHelper = $this->createMock(MetadataHelperInterface::class);
        $this->listener = new CreateFieldDefinitionListener($this->metadataHelper);
    }
}
