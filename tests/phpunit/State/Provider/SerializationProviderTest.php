<?php

declare(strict_types=1);

namespace LAG\AdminBundle\Tests\State\Provider;

use LAG\AdminBundle\Resource\Metadata\CollectionOperationInterface;
use LAG\AdminBundle\Resource\Metadata\Create;
use LAG\AdminBundle\Resource\Metadata\Delete;
use LAG\AdminBundle\Resource\Metadata\Get;
use LAG\AdminBundle\Resource\Metadata\Index;
use LAG\AdminBundle\Resource\Metadata\OperationInterface;
use LAG\AdminBundle\Resource\Metadata\Resource;
use LAG\AdminBundle\Resource\Metadata\Update;
use LAG\AdminBundle\State\Provider\ProviderInterface;
use LAG\AdminBundle\State\Provider\SerializationProvider;
use LAG\AdminBundle\Tests\TestCase;
use PHPUnit\Framework\MockObject\MockObject;
use Symfony\Component\Serializer\Normalizer\AbstractNormalizer;
use Symfony\Component\Serializer\SerializerInterface;

class SerializationProviderTest extends TestCase
{
    private SerializationProvider $provider;
    private MockObject $decoratedProvider;
    private MockObject $serializer;

    /** @dataProvider operationsProvider */
    public function testProvide(OperationInterface $operation): void
    {
        $data = new \stdClass();
        $data->someProperty = 'a value';
        $expectedType = \stdClass::class;

        if ($operation instanceof CollectionOperationInterface) {
            $data = [$data];
            $expectedType .= '[]';
        }

        $resource = new Resource(name: 'my_resource', dataClass: \stdClass::class);
        $operation = $operation->withAjax(true)
            ->withResource($resource)
            ->withNormalizationContext(['groups' => ['my_group']])
        ;

        $this->decoratedProvider
            ->expects(self::once())
            ->method('provide')
            ->with($operation, [], ['json' => true])
            ->willReturn($data)
        ;
        $this->serializer
            ->expects(self::once())
            ->method('deserialize')
            ->with($data, $expectedType, 'json', ['groups' => ['my_group'], AbstractNormalizer::OBJECT_TO_POPULATE => $data])
            ->willReturn('{"some": "json"}')
        ;

        $returnedData = $this->provider->provide($operation, [], ['json' => true]);
        $this->assertEquals('{"some": "json"}', $returnedData);
    }

    /** @dataProvider operationsProvider */
    public function testProvideWithoutAjax(OperationInterface $operation): void
    {
        $operation = $operation->withAjax(false);
        $data = new \stdClass();

        $this->decoratedProvider
            ->expects(self::once())
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

    /** @dataProvider wrongContextProvider */
    public function testWithoutContext(array $context): void
    {
        $data = new \stdClass();

        $this->decoratedProvider
            ->expects(self::once())
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
            [new Get()],
            [new Create()],
            [new Update()],
            [new Delete()],
        ];
    }

    protected function setUp(): void
    {
        $this->decoratedProvider = self::createMock(ProviderInterface::class);
        $this->serializer = self::createMock(SerializerInterface::class);
        $this->provider = new SerializationProvider(
            $this->decoratedProvider,
            $this->serializer,
        );
    }
}
