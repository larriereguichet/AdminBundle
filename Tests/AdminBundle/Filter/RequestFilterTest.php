<?php

namespace LAG\AdminBundle\Tests\AdminBundle\Filter;

use LAG\AdminBundle\Filter\RequestFilter;
use LAG\AdminBundle\Tests\AdminTestBase;
use Symfony\Component\HttpFoundation\Request;

class RequestFilterTest extends AdminTestBase
{
    public function testConfigure()
    {
        $filter = new RequestFilter();
        $filter->configure([
            'filters'
        ], [
            'orders'
        ], 50);

        $this->assertEquals([], $filter->getCriteria());
        $this->assertEquals([
            'orders'
        ], $filter->getOrder());
        $this->assertEquals(50, $filter->getMaxPerPage());

        $request = $this->createMock(Request::class);
        $request
            ->method('get')
            ->willReturnCallback(function ($parameter) {
                if ($parameter == 'order') {
                    return 'asc';
                }

                if ($parameter == 'sort') {
                    return 'name';
                }

                if ($parameter == 'filters') {
                    return 'bamboo';
                }

                return null;
            });
        $filter->filter($request);

        $this->assertEquals([
            'name' => 'asc'
        ], $filter->getOrder());
        $this->assertEquals([
            'filters' => 'bamboo'
        ], $filter->getCriteria());
    }

    public function testFilter()
    {
        $filter = new RequestFilter();
        $filter->configure([
            'name'
        ], [
            'orders'
        ], 50);

        $request = new Request([
            'name' => 'toto',
            'page' => 53,
            'sort' => 'name',
            'order' => 'asc'
        ]);

        $filter->filter($request);

        $this->assertEquals([
            'name' => 'toto'
        ], $filter->getCriteria());
        $this->assertEquals([
            'name' => 'asc'
        ], $filter->getOrder());
        $this->assertEquals(50, $filter->getMaxPerPage());
        $this->assertEquals(1, $filter->getCurrentPage());
    }
}
