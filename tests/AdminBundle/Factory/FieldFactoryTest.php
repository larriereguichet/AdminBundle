<?php

namespace LAG\AdminBundle\Tests\Factory;

use LAG\AdminBundle\Configuration\ApplicationConfiguration;
use LAG\AdminBundle\Event\Events\FieldEvent;
use LAG\AdminBundle\Exception\Field\FieldConfigurationException;
use LAG\AdminBundle\Factory\FieldFactory;
use LAG\AdminBundle\Field\StringField;
use LAG\AdminBundle\Tests\TestCase;
use PHPUnit\Framework\MockObject\MockObject;
use stdClass;
use Symfony\Contracts\EventDispatcher\EventDispatcherInterface;

class FieldFactoryTest extends TestCase
{
    private FieldFactory $fieldFactory;
    private MockObject $eventDispatcher;
    private ApplicationConfiguration $applicationConfiguration;

    public function testCreate(): void
    {
        $this
            ->eventDispatcher
            ->expects($this->exactly(3))
            ->method('dispatch')
            ->willReturnCallback(function ($event) {
                /* @var FieldEvent $event */
                $this->assertInstanceOf(FieldEvent::class, $event);

                $this->assertEquals([
                    'template' => 'string.html.twig',
                ], $event->getOptions());

                return $event;
            })
        ;
        $field = $this->fieldFactory->create('name', [
            'type' => 'string',
            'options' => ['template' => 'string.html.twig'],
        ]);

        $this->assertEquals([
            'template' => 'string.html.twig',
            'length' => 100,
            'replace' => '...',
            'translate_title' => true,
            'attr' => [
                'class' => 'admin-field admin-field-string',
            ],
            'header_attr' => [
                'class' => 'admin-header admin-header-string',
            ],
            'label' => null,
            'mapped' => false,
            'property_path' => 'name',
            'translation' => false,
            'translation_domain' => null,
            'sortable' => true,
        ], $field->getOptions());
        $this->assertEquals('name', $field->getName());
    }

    public function testCreateWithException(): void
    {
        $this->expectException(FieldConfigurationException::class);
        $this->fieldFactory->create('name', ['type' => stdClass::class]);
    }

    protected function setUp(): void
    {
        $this->eventDispatcher = $this->createMock(EventDispatcherInterface::class);
        $this->applicationConfiguration = new ApplicationConfiguration(['resources_path' => 'test']);

        $this->fieldFactory = new FieldFactory(
            $this->eventDispatcher,
            $this->applicationConfiguration,
            [
                'string' => StringField::class,
            ]
        );
    }
}
