<?php

declare(strict_types=1);

namespace LAG\AdminBundle\Tests\State\Provider;

use LAG\AdminBundle\Exception\Exception;
use LAG\AdminBundle\Resource\Metadata\Create;
use LAG\AdminBundle\Resource\Metadata\Delete;
use LAG\AdminBundle\Resource\Metadata\Get;
use LAG\AdminBundle\Resource\Metadata\Index;
use LAG\AdminBundle\Resource\Metadata\OperationInterface;
use LAG\AdminBundle\Resource\Metadata\Resource;
use LAG\AdminBundle\Resource\Metadata\Update;
use LAG\AdminBundle\State\Provider\CompositeProvider;
use LAG\AdminBundle\State\Provider\ProviderInterface;
use LAG\AdminBundle\Tests\TestCase;

class CompositeProviderTest extends TestCase
{
    /** @dataProvider operationsProvider */
    public function testProvide(OperationInterface $operation): void
    {
        $customProvider = self::createMock(ProviderInterface::class);
        $nevenCalledProvider = self::createMock(ProviderInterface::class);
        $operation = $operation->withProvider($customProvider::class);

        $customProvider
            ->expects(self::once())
            ->method('provide')
            ->with($operation, ['code' => 'abcd'], ['groups' => 'test'])
        ;

        $provider = new CompositeProvider([$customProvider, $nevenCalledProvider]);
        $provider->provide($operation, ['code' => 'abcd'], ['groups' => 'test']);
    }

    /** @dataProvider operationsProvider */
    public function testProvideWithoutProvider(OperationInterface $operation): void
    {
        $resource = new Resource(name: 'my_resource');
        $operation = $operation->withResource($resource);

        $this->expectExceptionMessage(\sprintf(
            'The admin resource "%s" and operation "%s" is not supported by any provider',
            'my_resource',
            $operation->getName()
        ));
        $this->expectException(Exception::class);

        $provider = new CompositeProvider();
        $provider->provide($operation, ['what-ever'], ['some', 'thing']);
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
}
