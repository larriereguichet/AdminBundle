<?php

declare(strict_types=1);

namespace LAG\AdminBundle\Tests\Bridge\Doctrine\ORM\State;

use Doctrine\ORM\QueryBuilder;
use LAG\AdminBundle\Bridge\Doctrine\ORM\State\Provider\PaginationProvider;
use LAG\AdminBundle\Resource\Metadata\Create;
use LAG\AdminBundle\Resource\Metadata\Delete;
use LAG\AdminBundle\Resource\Metadata\Get;
use LAG\AdminBundle\Resource\Metadata\Index;
use LAG\AdminBundle\Resource\Metadata\OperationInterface;
use LAG\AdminBundle\Resource\Metadata\Update;
use LAG\AdminBundle\State\Provider\ProviderInterface;
use Pagerfanta\PagerfantaInterface;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

final class PaginationProviderTest extends TestCase
{
    private PaginationProvider $provider;
    private MockObject $decorated;

    #[Test]
    public function itPaginatesData(): void
    {
        $data = self::createMock(QueryBuilder::class);

        $operation = new Index(pagination: true);
        $uriVariables = ['some_variable' => 'some_value'];
        $context = ['some_context' => 'some_context_value'];

        $this->decorated
            ->expects(self::once())
            ->method('provide')
            ->with($operation, $uriVariables, $context)
            ->willReturn($data)
        ;

        $pager = $this->provider->provide($operation, $uriVariables, $context);

        self::assertInstanceOf(PagerfantaInterface::class, $pager);
    }

    #[Test]
    #[DataProvider(methodName: 'noCollectionOperations')]
    public function itDoesNotPaginateDataIfNotCollection(OperationInterface $operation): void
    {
        $uriVariables = ['some_variable' => 'some_value'];
        $context = ['some_context' => 'some_context_value'];
        $data = new \stdClass();

        $this->decorated
            ->expects(self::once())
            ->method('provide')
            ->with($operation, $uriVariables, $context)
            ->willReturn($data)
        ;

        $returnedData = $this->provider->provide($operation, $uriVariables, $context);

        self::assertEquals($data, $returnedData);
    }

    public static function noCollectionOperations(): iterable
    {
        yield [new Get()];
        yield [new Create()];
        yield [new Update()];
        yield [new Delete()];
    }

    protected function setUp(): void
    {
        $this->decorated = self::createMock(ProviderInterface::class);
        $this->provider = new PaginationProvider($this->decorated);
    }
}
