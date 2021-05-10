<?php

namespace LAG\AdminBundle\Tests\Bridge\Doctrine\DataHandler;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Query;
use Doctrine\ORM\QueryBuilder;
use LAG\AdminBundle\Bridge\Doctrine\DataHandler\ResultsHandler;
use LAG\AdminBundle\Bridge\Doctrine\DataSource\ORMDataSource;
use LAG\AdminBundle\DataProvider\DataSourceInterface;
use LAG\AdminBundle\Exception\Exception;
use LAG\AdminBundle\Tests\TestCase;
use Pagerfanta\Pagerfanta;

class ResultHandlerTest extends TestCase
{
    private ResultsHandler $resultsHandler;

    public function assertServiceConfigured(): void
    {
        $this->assertServiceExists(ResultsHandler::class);
    }

    /**
     * @dataProvider supportsDataProvider
     */
    public function testSupports($dataSource, $supports): void
    {
        $this->assertEquals($this->resultsHandler->supports($dataSource), $supports);
    }

    public function supportsDataProvider(): array
    {
        return [
            [$this->createMock(ORMDataSource::class), true],
            [new FakeDataSource(), false],
        ];
    }

    public function testHandleWithQueryData(): void
    {
        $query = $this->createMock(Query::class);
        $query
            ->expects($this->once())
            ->method('getResult')
            ->willReturn([
                'data',
            ])
        ;

        $dataSource = $this->createMock(DataSourceInterface::class);
        $dataSource
            ->expects($this->exactly(2))
            ->method('getData')
            ->willReturn($query)
        ;
        $result = $this->resultsHandler->handle($dataSource);

        $this->assertEquals(['data'], $result);
    }

    public function testHandleWithQueryBuilderData(): void
    {
        $query = $this->createMock(Query::class);
        $query
            ->expects($this->once())
            ->method('getResult')
            ->willReturn([
                'data',
            ])
        ;
        $queryBuilder = $this->createMock(QueryBuilder::class);
        $queryBuilder
            ->expects($this->once())
            ->method('getQuery')
            ->willReturn($query)
        ;

        $dataSource = $this->createMock(DataSourceInterface::class);
        $dataSource
            ->expects($this->exactly(3))
            ->method('getData')
            ->willReturn($queryBuilder)
        ;
        $result = $this->resultsHandler->handle($dataSource);

        $this->assertEquals(['data'], $result);
    }

    public function testHandleWithArrayData(): void
    {
        $dataSource = $this->createMock(DataSourceInterface::class);
        $dataSource
            ->expects($this->exactly(4))
            ->method('getData')
            ->willReturn(['data'])
        ;
        $result = $this->resultsHandler->handle($dataSource);

        $this->assertEquals(new ArrayCollection(['data']), $result);
    }

    public function testHandleWithStringData(): void
    {
        $dataSource = $this->createMock(DataSourceInterface::class);
        $dataSource
            ->expects($this->exactly(4))
            ->method('getData')
            ->willReturn('data')
        ;
        $result = $this->resultsHandler->handle($dataSource);

        $this->assertEquals('data', $result);
    }

    /**
     * @dataProvider handleWithPaginatedDataProvider
     */
    public function testHandleWithPaginatedData($data, bool $throws): void
    {
        $dataSource = $this->createMock(DataSourceInterface::class);
        $dataSource
            ->expects($this->exactly(1))
            ->method('getData')
            ->willReturn($data)
        ;
        $dataSource
            ->expects($this->once())
            ->method('isPaginated')
            ->willReturn(true)
        ;

        if ($throws) {
            $this->expectException(Exception::class);
            $this->resultsHandler->handle($dataSource);

            return;
        }
        $dataSource
            ->expects($this->once())
            ->method('getPage')
            ->willReturn(1)
        ;
        $dataSource
            ->expects($this->once())
            ->method('getMaxPerPage')
            ->willReturn(25)
        ;


        $result = $this->resultsHandler->handle($dataSource);
        $this->assertInstanceOf(Pagerfanta::class, $result);
    }

    public function handleWithPaginatedDataProvider(): array
    {
        return [
            [$this->createMock(QueryBuilder::class), false],
            [new ArrayCollection([]), false],
            [[], false],
            [$this->createIterator(), false],
            [false, true],
        ];
    }

    protected function setUp(): void
    {
        $this->resultsHandler = new ResultsHandler();
    }

    private function createIterator(): \Iterator
    {
        yield 'test';
    }
}
