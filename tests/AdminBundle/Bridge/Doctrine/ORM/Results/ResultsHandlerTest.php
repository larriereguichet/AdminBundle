<?php

namespace LAG\AdminBundle\Tests\Bridge\Doctrine\ORM\Results;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Query;
use Doctrine\ORM\QueryBuilder;
use Iterator;
use LAG\AdminBundle\Bridge\Doctrine\ORM\Results\ResultsHandler;
use LAG\AdminBundle\Exception\Exception;
use LAG\AdminBundle\Tests\TestCase;
use Pagerfanta\Pagerfanta;
use stdClass;

class ResultsHandlerTest extends TestCase
{
    public function testHandleArray(): void
    {
        $handler = new ResultsHandler();

        $data = [
            'data' => 'Oh Yeah !',
        ];

        $results = $handler->handle($data, false);
        $this->assertEquals(new ArrayCollection($data), $results);

        $results = $handler->handle($data, true);
        $this->assertInstanceOf(Pagerfanta::class, $results);
    }

    public function testHandleWithQueryBuilder(): void
    {
        $handler = new ResultsHandler();

        $data = new stdClass();
        $data->test = true;
        $query = $this->createMock(Query::class);
        $query
            ->expects($this->atLeastOnce())
            ->method('getResult')
            ->willReturn($data)
        ;
        $results = $handler->handle($query, false);
        $this->assertEquals($data, $results);

        $queryBuilder = $this->createMock(QueryBuilder::class);
        $queryBuilder
            ->expects($this->atLeastOnce())
            ->method('getQuery')
            ->willReturn($query)
        ;
        $results = $handler->handle($queryBuilder, false);
        $this->assertEquals($data, $results);

        $results = $handler->handle($queryBuilder, true);
        $this->assertInstanceOf(Pagerfanta::class, $results);
    }

    public function testHandleIterable(): void
    {
        $handler = new ResultsHandler();
        $data = $this->getIterator();

        $results = $handler->handle($data, false);
        $this->assertEquals($data, $results);

        $results = $handler->handle($data, true);
        $this->assertInstanceOf(Pagerfanta::class, $results);
    }

    public function testHandleCollection(): void
    {
        $handler = new ResultsHandler();
        $data = new ArrayCollection();

        $results = $handler->handle($data, false);
        $this->assertEquals($data, $results);

        $results = $handler->handle($data, true);
        $this->assertInstanceOf(Pagerfanta::class, $results);
    }

    public function testHandleWrongType(): void
    {
        $handler = new ResultsHandler();
        $data = new stdClass();

        $this->expectException(Exception::class);
        $handler->handle($data, true);
    }

    private function getIterator(): Iterator
    {
        yield 'test';
    }
}
