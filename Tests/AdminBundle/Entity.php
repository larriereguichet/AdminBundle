<?php

namespace LAG\AdminBundle\Tests\Entity;

use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;

/**
 *
 * @ORM\Table(name="admin_test")
 * @ORM\Entity(repositoryClass="LAG\AdminBundle\Repository\GenericRepository")
 * @ORM\HasLifecycleCallbacks()
 */
class EntityTest
{

}
