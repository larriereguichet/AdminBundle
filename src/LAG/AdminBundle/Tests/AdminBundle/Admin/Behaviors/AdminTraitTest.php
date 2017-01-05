<?php

namespace LAG\AdminBundle\Tests\AdminBundle\Admin\Behaviors;

use LAG\AdminBundle\Admin\Behaviors\AdminTrait;
use LAG\AdminBundle\Tests\AdminTestBase;
use Pagerfanta\Pagerfanta;
use stdClass;

class AdminTraitTest extends AdminTestBase
{
    use AdminTrait;

    protected $pager;

    /**
     * GetPager method should return the given pager.
     */
    public function testGetPager()
    {
        $this->pager = $this->createMock(Pagerfanta::class);
        $this->assertEquals($this->pager, $this->getPager());
    }

    /**
     * GetUniqueEntityLabel method should return the name of an entity.
     */
    public function testGetUniqueEntityLabel()
    {
        $this->pager = $this->createMock(Pagerfanta::class);
        $this->assertEquals('my_entity', $this->getUniqueEntityLabel());
    }

    /**
     * Return the current unique entity.
     *
     * @return object
     */
    public function getUniqueEntity()
    {
        $entity = new stdClass();
        $entity->name = 'my_entity';

        return $entity;
    }
}
