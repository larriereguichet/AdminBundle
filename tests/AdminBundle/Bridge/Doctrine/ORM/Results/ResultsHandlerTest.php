<?php

namespace LAG\AdminBundle\Tests\Bridge\Doctrine\ORM\Results;

use Doctrine\ORM\Query;
use Doctrine\ORM\QueryBuilder;
use LAG\AdminBundle\Bridge\Doctrine\ORM\Results\ResultsHandler;
use LAG\AdminBundle\Tests\AdminTestBase;
use LAG\AdminBundle\Tests\Fixtures\FakeEntity;
use Pagerfanta\Pagerfanta;

class ResultsHandlerTest extends AdminTestBase
{
    public function testHandleArray()
    {
        $handler = new ResultsHandler();

        $data = [
            'data' => 'Oh Yeah !',
        ];

        $results = $handler->handle($data, false, 1, 25);
        $this->assertEquals($data, $results);

        $results = $handler->handle($data, true, 1, 25);
        $this->assertIsArray($results);
    }
    
    public function testHandleQuery(): void
    {
        $handler = new ResultsHandler();
    
        $query = $this->createMock(Query::class);
        $query
            ->expects($this->atLeastOnce())
            ->method('getResult')
            ->willReturn('data !')
        ;
        $results = $handler->handle($query, false, 1, 25);
        $this->assertEquals('data !', $results);
    }
}
