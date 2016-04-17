<?php

namespace Tests\AdminBundle\Admin\Behaviors;

use LAG\AdminBundle\Admin\Behaviors\EntityLabelTrait;
use LAG\AdminBundle\Tests\AdminTestBase;
use stdClass;

class EntityLabelTraitTest extends AdminTestBase
{
    use EntityLabelTrait;

    public function testGetEntityLabel()
    {
        $entity = new stdClass();
        $entity->label = 'panda';
        $this->assertEquals('panda', $this->getEntityLabel($entity));


        $entity = new stdClass();
        $entity->title = 'panda';
        $this->assertEquals('panda', $this->getEntityLabel($entity));

        $entity = new stdClass();
        $entity->__toString = 'panda';
        $this->assertEquals('panda', $this->getEntityLabel($entity));

        $entity = new stdClass();
        $entity->content = 'panda';
        $this->assertEquals('panda', $this->getEntityLabel($entity));

        $entity = new stdClass();
        $entity->id = 'panda';
        $this->assertEquals('panda', $this->getEntityLabel($entity));
    }
}
