<?php

declare(strict_types=1);

namespace LAG\AdminBundle\Tests\State\Provider;

use LAG\AdminBundle\Exception\Exception;
use LAG\AdminBundle\Metadata\Create;
use LAG\AdminBundle\Metadata\Delete;
use LAG\AdminBundle\Metadata\Index;
use LAG\AdminBundle\Metadata\OperationInterface;
use LAG\AdminBundle\Metadata\Resource;
use LAG\AdminBundle\Metadata\Show;
use LAG\AdminBundle\Metadata\Update;
use LAG\AdminBundle\State\Provider\CompositeProvider;
use LAG\AdminBundle\State\Provider\ProviderInterface;
use LAG\AdminBundle\Tests\TestCase;
use PHPUnit\Framework\Attributes\DataProvider;

final class CompositeProviderTest extends TestCase
{
    #[DataProvider('operationsProvider')]
    public function testProvide(OperationInterface $operation): void
    {
        $customProvider = $this->createMock(ProviderInterface::class);
        $nevenCalledProvider = $this->createMock(ProviderInterface::class);
        $operation = $operation->withProvider($customProvider::class);

        $customProvider
            ->expects($this->once())
            ->method('provide')
            ->with($operation, ['code' => 'abcd'], ['groups' => 'test']);

        $provider = new CompositeProvider([$customProvider, $nevenCalledProvider]);
        $provider->provide($operation, ['code' => 'abcd'], ['groups' => 'test']);
    }

    #[DataProvider('operationsProvider')]
    public function testProvideWithoutProvider(OperationInterface $operation): void
    {
        $resource = new Resource(name: 'my_resource');
        $operation = $operation->setResource($resource);

        $this->expectExceptionObject(new Exception(\sprintf(
            'The resource "%s" and operation "%s" in the application "%s" is not supported by any provider',
            $operation->getResource()->getName(),
            $operation->getFullName(),
            $operation->getResource()->getApplication(),
        )));

        $provider = new CompositeProvider();
        $provider->provide($operation, ['key' => 'what-ever'], ['some' => 'thing']);
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
}
