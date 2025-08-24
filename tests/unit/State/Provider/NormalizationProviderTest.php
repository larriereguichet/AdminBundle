<?php

declare(strict_types=1);

namespace LAG\AdminBundle\Tests\State\Provider;

use Doctrine\Common\Collections\ArrayCollection;
use LAG\AdminBundle\Metadata\Create;
use LAG\AdminBundle\Metadata\Delete;
use LAG\AdminBundle\Metadata\Index;
use LAG\AdminBundle\Metadata\OperationInterface;
use LAG\AdminBundle\Metadata\Resource;
use LAG\AdminBundle\Metadata\Show;
use LAG\AdminBundle\Metadata\Update;
use LAG\AdminBundle\State\Provider\NormalizationProvider;
use LAG\AdminBundle\State\Provider\ProviderInterface;
use LAG\AdminBundle\Tests\TestCase;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\MockObject\MockObject;
use Symfony\Component\Serializer\Normalizer\DenormalizerInterface;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;

class NormalizationProviderTest extends TestCase
{
    private NormalizationProvider $provider;
    private MockObject $decoratedProvider;
    private MockObject $normalizer;
    private MockObject $denormalizer;

    #[DataProvider('supportsProvider')]
    public function testSupports(OperationInterface $operation, mixed $data, bool $supports, ?string $expectedType = null): void
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

        yield [new Index(output: null), null, false];
        yield [new Show(output: null), null, false];
        yield [new Show(output: 'Output'), ['value'], false];
        yield [new Create(output: null), null, false];
        yield [new Update(output: null), null, false];
        yield [new Delete(output: null), null, false];
        yield [new Index(output: null), $fake, false];
        yield [new Index(output: null), new ArrayCollection(), false];
        yield [new Index(output: 'TestClass'), $fake, false];
        yield [
            (new Show(output: 'TestClass'))->setResource(new Resource(resourceClass: 'OtherClass')),
            new ArrayCollection(),
            false,
        ];
        yield [
            (new Show(output: 'TestClass'))->setResource(new Resource(resourceClass: 'OtherClass')),
            new ArrayCollection(),
            false,
        ];
        yield [
            (new Show(output: 'TestClass'))->setResource(new Resource(resourceClass: 'OtherClass')),
            new ArrayCollection(),
            false,
        ];

        yield [
            (new Show(
                normalizationContext: ['groups' => ['normalization']],
                denormalizationContext: ['groups' => ['denormalization']],
                output: 'Output',
            ))->setResource(new Resource(resourceClass: \stdClass::class)),
            $fake,
            true,
            'Output',
        ];
        yield [
            (new Index(
                normalizationContext: ['groups' => ['normalization']],
                denormalizationContext: ['groups' => ['denormalization']],
                output: 'Output',
            ))->setResource(new Resource(resourceClass: \stdClass::class)),
            [$fake],
            true,
            'Output[]',
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
