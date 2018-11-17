<?php

namespace LAG\AdminBundle\Tests\Filter;

use LAG\AdminBundle\Filter\Filter;
use LAG\AdminBundle\Tests\AdminTestBase;

class FilterTest extends AdminTestBase
{
    public function testGettersAndSetters()
    {
        $filter = new Filter(
            'my_little_filter',
            3656.23,
            '=',
            'and'
        );

        $this->assertEquals('my_little_filter', $filter->getName());
        $this->assertEquals(3656.23, $filter->getValue());
        $this->assertEquals('=', $filter->getComparator());
        $this->assertEquals('and', $filter->getOperator());
    }
}
