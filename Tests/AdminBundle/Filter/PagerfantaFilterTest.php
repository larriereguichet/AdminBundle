<?php

namespace LAG\AdminBundle\Tests\AdminBundle\Filter;

use LAG\AdminBundle\Filter\PagerfantaFilter;
use LAG\AdminBundle\Tests\AdminTestBase;
use Symfony\Component\HttpFoundation\Request;

class PagerfantaFilterTest extends AdminTestBase
{
    public function testConfigure()
    {
        $filter = new PagerfantaFilter();
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
    }

    public function testFilter()
    {
        $filter = new PagerfantaFilter();
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
        $this->assertEquals(53, $filter->getCurrentPage());
    }
}
