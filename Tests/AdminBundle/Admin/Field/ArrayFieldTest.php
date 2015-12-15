<?php

namespace AdminBundle\Admin\Field;

use Doctrine\Common\Collections\ArrayCollection;
use LAG\AdminBundle\Admin\Field\ArrayField;
use LAG\AdminBundle\Tests\Base;

class ArrayFieldTest extends Base
{
    public function testRender()
    {
        $arrayField = new ArrayField();
        $arrayField->setOptions([
            'glue' => ', '
        ]);
        // test simple string array
        $value = [
            'test',
            'lol',
            'panda'
        ];
        $this->assertEquals('test, lol, panda', $arrayField->render($value));
        // test with array collection
        $value = new ArrayCollection();
        $value->add('test');
        $value->add('lol');
        $value->add('panda');
        $this->assertEquals('test, lol, panda', $arrayField->render($value));
    }
}
