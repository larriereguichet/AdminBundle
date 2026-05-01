<?php

declare(strict_types=1);

namespace LAG\AdminBundle\Tests\Unit\Request\ContextBuilder;

use LAG\AdminBundle\Metadata\Attribute\Create;
use LAG\AdminBundle\Metadata\Attribute\Delete;
use LAG\AdminBundle\Metadata\Attribute\Index;
use LAG\AdminBundle\Metadata\Attribute\Show;
use LAG\AdminBundle\Metadata\Attribute\Update;
use LAG\AdminBundle\Metadata\OperationInterface;
use LAG\AdminBundle\Request\ContextBuilder\SortingContextBuilder;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\Request;

final class SortingContextBuilderTest extends TestCase
{
    private SortingContextBuilder $provider;

    #[Test]
    public function itAddsSortingContext(): void
    {
        $request = new Request(query: [
            '_page' => 23,
            'sort' => 'name',
            'order' => 'desc',
        ]);
        $operation = new Index()->withPageParameter('_page');

        $context = $this->provider->buildContext($operation, $request);

        self::assertEquals('name', $context['sort']);
        self::assertEquals('desc', $context['order']);
    }

    #[Test]
    #[DataProvider('nonCollectionOperations')]
    public function itDoesNotAddContextOnNonCollectionOperation(OperationInterface $operation): void
    {
        $request = new Request();
        self::assertFalse($this->provider->supports($request, $operation, null));
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
        $this->provider = new SortingContextBuilder();
    }
}
