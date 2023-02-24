<?php

namespace LAG\AdminBundle\Tests\Metadata\Factory;

use LAG\AdminBundle\Metadata\Factory\ResourceFactory;
use LAG\AdminBundle\Tests\TestCase;
use PHPUnit\Framework\MockObject\MockObject;

class ResourceFactoryTest extends TestCase
{
    private ResourceFactory $resourceFactory;
    private MockObject $eventDispatcher;
    private MockObject $operationFactory;
}
