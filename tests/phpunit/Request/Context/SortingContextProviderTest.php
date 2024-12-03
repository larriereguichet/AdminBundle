<?php

declare(strict_types=1);

namespace LAG\AdminBundle\Tests\Request\Context;

use LAG\AdminBundle\Request\Context\ContextProviderInterface;
use LAG\AdminBundle\Request\Context\SortingContextProvider;
use LAG\AdminBundle\Resource\Metadata\Create;
use LAG\AdminBundle\Resource\Metadata\Delete;
use LAG\AdminBundle\Resource\Metadata\Index;
use LAG\AdminBundle\Resource\Metadata\OperationInterface;
use LAG\AdminBundle\Resource\Metadata\Show;
use LAG\AdminBundle\Resource\Metadata\Update;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\Request;

final class SortingContextProviderTest extends TestCase
{
    private SortingContextProvider $provider;
    private MockObject $decorated;

    #[Test]
    public function itAddsSortingContext(): void
    {
        $request = new Request(query: [
            '_page' => 23,
            'sort' => 'name',
            'order' => 'desc',
        ]);
        $operation = (new Index())->withPageParameter('_page');

        $this->decorated
            ->expects(self::once())
            ->method('getContext')
            ->with($operation, $request)
            ->willReturn(['some-key' => 'some-value'])
        ;

        $context = $this->provider->getContext($operation, $request);

        self::assertEquals(23, $context['_page']);
        self::assertEquals('name', $context['sort']);
        self::assertEquals('DESC', $context['order']);
        self::assertEquals('some-value', $context['some-key']);
    }

    #[Test]
    #[DataProvider(methodName: 'nonCollectionOperations')]
    public function itDoesNotAddContextOnNonCollectionOperation(OperationInterface $operation): void
    {
        $request = new Request();

        $this->decorated
            ->expects(self::once())
            ->method('getContext')
            ->with($operation, $request)
            ->willReturn(['some-key' => 'some-value'])
        ;
        $context = $this->provider->getContext($operation, $request);

        self::assertEquals(['some-key' => 'some-value'], $context);
    }

    public static function nonCollectionOperations(): iterable
    {
        yield [new Create()];
        yield [new Update()];
        yield [new Delete()];
        yield [new Show()];
    }

    protected function setUp(): void
    {
        $this->decorated = self::createMock(ContextProviderInterface::class);
        $this->provider = new SortingContextProvider($this->decorated);
    }
}
