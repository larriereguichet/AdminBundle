<?php

declare(strict_types=1);

namespace LAG\AdminBundle\Tests\Unit\State\Provider;

use LAG\AdminBundle\Metadata\Attribute\Create;
use LAG\AdminBundle\Metadata\Attribute\Delete;
use LAG\AdminBundle\Metadata\Attribute\Index;
use LAG\AdminBundle\Metadata\Attribute\Resource;
use LAG\AdminBundle\Metadata\Attribute\Show;
use LAG\AdminBundle\Metadata\Attribute\Update;
use LAG\AdminBundle\Metadata\CollectionOperationInterface;
use LAG\AdminBundle\Metadata\OperationInterface;
use LAG\AdminBundle\State\Provider\ProviderInterface;
use LAG\AdminBundle\State\Provider\SerializationProvider;
use LAG\AdminBundle\Tests\Unit\TestCase;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\MockObject\MockObject;
use Symfony\Component\Serializer\Normalizer\AbstractNormalizer;
use Symfony\Component\Serializer\SerializerInterface;

class SerializationProviderTest extends TestCase
{
    private SerializationProvider $provider;
    private MockObject $decoratedProvider;
    private MockObject $serializer;

    #[DataProvider('operationsProvider')]
    public function testProvide(OperationInterface $operation): void
    {
        $data = new \stdClass();
        $data->someProperty = 'a value';
        $expectedType = \stdClass::class;

        if ($operation instanceof CollectionOperationInterface) {
            $data = [$data];
            $expectedType .= '[]';
        }

        $resource = new Resource(name: 'my_resource', resourceClass: \stdClass::class);
        $operation = $operation->withAjax(true)
            ->setResource($resource)
            ->withNormalizationContext(['groups' => ['my_group']])
        ;

        $this->decoratedProvider
            ->expects($this->once())
            ->method('provide')
            ->with($operation, [], ['json' => true])
            ->willReturn($data)
        ;
        $this->serializer
            ->expects($this->once())
            ->method('deserialize')
            ->with($data, $expectedType, 'json', ['groups' => ['my_group'], AbstractNormalizer::OBJECT_TO_POPULATE => $data])
            ->willReturn('{"some": "json"}')
        ;

        $returnedData = $this->provider->provide($operation, [], ['json' => true]);
        $this->assertEquals('{"some": "json"}', $returnedData);
    }

    #[DataProvider('operationsProvider')]
    public function testProvideWithoutAjax(OperationInterface $operation): void
    {
        $operation = $operation->withAjax(false);
        $data = new \stdClass();

        $this->decoratedProvider
            ->expects($this->once())
            ->method('provide')
            ->with($operation, [], ['json' => true])
            ->willReturn($data)
        ;
        $this->serializer
            ->expects($this->never())
            ->method('serialize')
        ;

        $this->provider->provide($operation, [], ['json' => true]);
    }

    #[DataProvider('wrongContextProvider')]
    public function testWithoutContext(array $context): void
    {
        $data = new \stdClass();

        $this->decoratedProvider
            ->expects($this->once())
            ->method('provide')
            ->with(new Index(), [], $context)
            ->willReturn($data)
        ;
        $this->serializer
            ->expects($this->never())
            ->method('serialize')
        ;

        $returnedData = $this->provider->provide(new Index(), [], $context);
        $this->assertEquals($data, $returnedData);
    }

    public static function wrongContextProvider(): array
    {
        return [
            [['json' => false]],
            [['json' => 'false']],
            [['json' => 'true']],
            [['json' => '']],
            [['jsons' => true]],
            [['json' => 'json']],
        ];
    }

    public static function operationsProvider(): array
    {
        return [
            [new Index()],
            [new Show()],
            [new Create()],
            [new Update()],
            [new Delete()],
        ];
    }

    protected function setUp(): void
    {
        $this->decoratedProvider = $this->createMock(ProviderInterface::class);
        $this->serializer = $this->createMock(SerializerInterface::class);
        $this->provider = new SerializationProvider(
            $this->decoratedProvider,
            $this->serializer,
        );
    }
}
