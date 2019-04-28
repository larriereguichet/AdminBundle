<?php

namespace LAG\AdminBundle\Tests\Field;

use LAG\AdminBundle\Field\AutoField;
use LAG\AdminBundle\Tests\AdminTestBase;

class AutoFieldTest extends AdminTestBase
{
    public function testRender()
    {
        $field = new AutoField('my_field');
        $this->assertEquals('My Field,is a,panda', $field->render([
            'My Field',
            'is a',
            'panda',
        ]));
        $this->assertEquals('', $field->render(null));
        $this->assertEquals('My Panda', $field->render('My Panda'));
        $this->assertFalse($field->isSortable());
    }
}
