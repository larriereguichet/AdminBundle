<?php

declare(strict_types=1);

namespace LAG\AdminBundle\Tests\Unit\Request\ContextBuilder;

use LAG\AdminBundle\Metadata\Attribute\Create;
use LAG\AdminBundle\Metadata\Attribute\Delete;
use LAG\AdminBundle\Metadata\Attribute\Index;
use LAG\AdminBundle\Metadata\Attribute\Show;
use LAG\AdminBundle\Metadata\Attribute\Update;
use LAG\AdminBundle\Metadata\GridInterface;
use LAG\AdminBundle\Metadata\OperationInterface;
use LAG\AdminBundle\Request\ContextBuilder\ContextBuilderInterface;
use LAG\AdminBundle\Request\ContextBuilder\SortingContextBuilder;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\Request;

final class SortingContextBuilderTest extends TestCase
{
    private SortingContextBuilder $provider;
    private MockObject $contextBuilder;

    #[Test]
    public function itAddsSortingContext(): void
    {
        $request = new Request(query: [
            'sort' => 'name',
            'order' => 'desc',
        ]);
        $operation = new Index();
        $grid = $this->createStub(GridInterface::class);
        $grid->method('getSortParameter')->willReturn('sort');
        $grid->method('getOrderParameter')->willReturn('order');

        $this->contextBuilder
            ->method('buildContext')
            ->with($request, $operation, $grid)
            ->willReturn([])
        ;

        $context = $this->provider->buildContext($request, $operation, $grid);

        self::assertEquals('name', $context['sort']);
        self::assertEquals('desc', $context['order']);
    }

    #[Test]
    public function itReadsTheSortAndOrderFromTheGridParameters(): void
    {
        $request = new Request(query: [
            'book_sort' => 'title',
            'book_order' => 'asc',
        ]);
        $operation = new Index();
        $grid = $this->createStub(GridInterface::class);
        $grid->method('getSortParameter')->willReturn('book_sort');
        $grid->method('getOrderParameter')->willReturn('book_order');

        $this->contextBuilder
            ->method('buildContext')
            ->with($request, $operation, $grid)
            ->willReturn([])
        ;

        $context = $this->provider->buildContext($request, $operation, $grid);

        self::assertEquals('title', $context['sort']);
        self::assertEquals('asc', $context['order']);
    }

    #[Test]
    public function itDoesNotAddTheOrderWhenTheParameterIsMissing(): void
    {
        $request = new Request(query: ['sort' => 'name']);
        $operation = new Index();
        $grid = $this->createStub(GridInterface::class);
        $grid->method('getSortParameter')->willReturn('sort');
        $grid->method('getOrderParameter')->willReturn('order');

        $this->contextBuilder
            ->method('buildContext')
            ->with($request, $operation, $grid)
            ->willReturn([])
        ;

        $context = $this->provider->buildContext($request, $operation, $grid);

        self::assertEquals('name', $context['sort']);
        self::assertArrayNotHasKey('order', $context);
    }

    #[Test]
    #[DataProvider('nonCollectionOperations')]
    public function itDoesNotAddContextOnNonCollectionOperation(OperationInterface $operation): void
    {
        $request = new Request();
        $grid = $this->createStub(GridInterface::class);

        $this->contextBuilder
            ->method('buildContext')
            ->with($request, $operation, $grid)
            ->willReturn([])
        ;

        $context = $this->provider->buildContext($request, $operation, $grid);

        self::assertEquals([], $context);
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
        $this->contextBuilder = $this->createMock(ContextBuilderInterface::class);
        $this->provider = new SortingContextBuilder($this->contextBuilder);
    }
}
