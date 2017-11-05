<?php

namespace LAG\AdminBundle\Tests\AdminBundle\Field;

use Doctrine\Common\Collections\ArrayCollection;
use LAG\AdminBundle\Field\Field\ArrayField;
use LAG\AdminBundle\Tests\AdminTestBase;
use stdClass;

class ArrayFieldTest extends AdminTestBase
{
    public function testRender()
    {
        $arrayField = new ArrayField('my-field');
        
        $this->setPrivateProperty($arrayField, 'options', [
            'glue' => ', ',
        ]);
    
        $content = $arrayField->render([
            'panda',
            'squirrel',
            'narwhal',
        ]);
    
        $this->assertEquals('panda, squirrel, narwhal', $content);
    }
    
    public function testRenderCollection()
    {
        $arrayField = new ArrayField('my-field');
        
        $this->setPrivateProperty($arrayField, 'options', [
            'glue' => ', ',
        ]);
    
        $collection = new ArrayCollection([
            'panda',
            'squirrel',
            'narwhal',
        ]);
        $content = $arrayField->render($collection);
        
        $this->assertEquals('panda, squirrel, narwhal', $content);
    }
    
    public function testRenderInvalidValue()
    {
        $arrayField = new ArrayField('my-field');
        
        $this->assertExceptionRaised(\Exception::class, function () use ($arrayField) {
            $arrayField->render('a string');
        });
        
        $this->assertExceptionRaised(\Exception::class, function () use ($arrayField) {
            $arrayField->render(12);
        });
    
        $this->assertExceptionRaised(\Exception::class, function () use ($arrayField) {
            $arrayField->render(new stdClass());
        });
    }
}
