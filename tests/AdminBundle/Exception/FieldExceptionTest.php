<?php

namespace LAG\AdminBundle\Tests\Exception;

use LAG\AdminBundle\Exception\Field\FieldTypeNotFoundException;
use LAG\AdminBundle\Tests\AdminTestBase;

class FieldExceptionTest extends AdminTestBase
{
    public function testException()
    {
        $exception = new FieldTypeNotFoundException('panda', 'bamboos', 'leaves');

        $this->assertEquals('No type found for the Field "leaves" in Action "bamboos" in Admin "panda"', $exception->getMessage());
    }
}
