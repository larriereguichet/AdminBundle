<?php

namespace LAG\AdminBundle\Tests\State\Provider;

use Doctrine\Common\Collections\ArrayCollection;
use LAG\AdminBundle\Metadata\Resource;
use LAG\AdminBundle\Metadata\Create;
use LAG\AdminBundle\Metadata\Delete;
use LAG\AdminBundle\Metadata\Get;
use LAG\AdminBundle\Metadata\GetCollection;
use LAG\AdminBundle\Metadata\OperationInterface;
use LAG\AdminBundle\Metadata\Update;
use LAG\AdminBundle\State\Provider\ProviderInterface;
use LAG\AdminBundle\State\Provider\NormalizationProvider;
use LAG\AdminBundle\Tests\TestCase;
use PHPUnit\Framework\MockObject\MockObject;
use Symfony\Component\Serializer\Normalizer\DenormalizerInterface;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;

class NormalizationProviderTest extends TestCase
{
    private NormalizationProvider $provider;
    private MockObject $decoratedProvider;
    private MockObject $normalizer;
    private MockObject $denormalizer;

    /** @dataProvider supportsProvider */
    public function testSupports(OperationInterface $operation, mixed $data, bool $supports, string $expectedType = null): void
    {
        $this
            ->decoratedProvider
            ->expects($this->once())
            ->method('provide')
            ->with($operation, ['id' => 666], ['a_key' => 'a_value'])
            ->willReturn($data)
        ;

        if ($supports) {
            $denormalizedData = new \stdClass();
            $denormalizedData->myProperty = 'test';

            $this
                ->normalizer
                ->expects($this->once())
                ->method('normalize')
                ->with($data, null, ['groups' => ['normalization']])
                ->willReturn(['normalized' => 'data'])
            ;
            $this
                ->denormalizer
                ->expects($this->once())
                ->method('denormalize')
                ->with(['normalized' => 'data'], $expectedType, null, ['groups' => ['denormalization']])
                ->willReturn($denormalizedData)
            ;


        } else {
            $this
                ->normalizer
                ->expects($this->never())
                ->method('normalize')
            ;
            $this
                ->denormalizer
                ->expects($this->never())
                ->method('denormalize')
            ;
        }
        $providedData = $this->provider->provide($operation, ['id' => 666], ['a_key' => 'a_value']);

        if ($supports) {
            $this->assertEquals($denormalizedData, $providedData);
        } else {
            $this->assertEquals($data, $providedData);
        }
    }

    public static function supportsProvider(): iterable
    {
        $fake = new \stdClass();
        $fake->myProperty = 'test';

        $denormalizedData = new \stdClass();
        $denormalizedData->myOtherProperty = 'otherTest';

        yield [new GetCollection(outputClass: null), null, false];
        yield [new Get(outputClass: null), null, false];
        yield [new Get(outputClass: 'OutputClass'), ['value'], false];
        yield [new Create(outputClass: null), null, false];
        yield [new Update(outputClass: null), null, false];
        yield [new Delete(outputClass: null), null, false];
        yield [new GetCollection(outputClass: null), $fake, false];
        yield [new GetCollection(outputClass: null), new ArrayCollection(), false];
        yield [new GetCollection(outputClass: 'TestClass'), $fake, false];
        yield [
            (new Get(outputClass: 'TestClass'))->withResource(new Resource(dataClass: 'OtherClass')),
            new ArrayCollection(),
            false,
        ];
        yield [
            (new Get(outputClass: 'TestClass'))->withResource(new Resource(dataClass: 'OtherClass')),
            new ArrayCollection(),
            false
        ];
        yield [
            (new Get(outputClass: 'TestClass'))->withResource(new Resource(dataClass: 'OtherClass')),
            new ArrayCollection(),
            false,
        ];


        yield [
            (new Get(
                outputClass: 'OutputClass',
                normalizationContext: ['groups' => ['normalization']],
                denormalizationContext: ['groups' => ['denormalization']],
            ))->withResource(new Resource(dataClass: \stdClass::class)),
            $fake,
            true,
            'OutputClass',
        ];
        yield [
            (new GetCollection(
                outputClass: 'OutputClass',
                normalizationContext: ['groups' => ['normalization']],
                denormalizationContext: ['groups' => ['denormalization']],
            ))->withResource(new Resource(dataClass: \stdClass::class)),
            [$fake],
            true,
            'OutputClass[]',
        ];
    }

    protected function setUp(): void
    {
        $this->decoratedProvider = $this->createMock(ProviderInterface::class);
        $this->normalizer = $this->createMock(NormalizerInterface::class);
        $this->denormalizer = $this->createMock(DenormalizerInterface::class);
        $this->provider = new NormalizationProvider(
            $this->decoratedProvider,
            $this->normalizer,
            $this->denormalizer,
        );
    }
}
