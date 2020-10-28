<?php

namespace LAG\AdminBundle\Tests\Bridge\Doctrine\ORM\Results;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Query;
use Doctrine\ORM\QueryBuilder;
use LAG\AdminBundle\Bridge\Doctrine\ORM\Results\ResultsHandler;
use LAG\AdminBundle\Tests\TestCase;
use Pagerfanta\Pagerfanta;
use stdClass;

class ResultsHandlerTest extends TestCase
{
    public function testHandleArray()
    {
        $handler = new ResultsHandler();

        $data = [
            'data' => 'Oh Yeah !',
        ];

        $results = $handler->handle($data, false, 1, 25);
        $this->assertEquals(new ArrayCollection($data), $results);

        $results = $handler->handle($data, true, 1, 25);
        $this->assertInstanceOf(Pagerfanta::class, $results);
    }

    public function testHandleWithQueryBuilder()
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
        $results = $handler->handle($query, false, 1, 25);
        $this->assertEquals($data, $results);

        $queryBuilder = $this->createMock(QueryBuilder::class);
        $queryBuilder
            ->expects($this->atLeastOnce())
            ->method('getQuery')
            ->willReturn($query)
        ;
        $results = $handler->handle($queryBuilder, false, 1, 25);
        $this->assertEquals($data, $results);

        $results = $handler->handle($queryBuilder, true, 1, 25);
        $this->assertInstanceOf(Pagerfanta::class, $results);
    }
}
