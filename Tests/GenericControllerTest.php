<?php

namespace BlueBear\AdminBundle\Tests;

use BlueBear\AdminBundle\Controller\GenericController;
use Symfony\Component\HttpFoundation\Request;

class GenericControllerTest extends Base
{
    public function testList()
    {
        $client = $this->initApplication();
        $container = $client->getKernel()->getContainer();

        $controller = new GenericController();
        $controller->setContainer($container);
        $request = new Request();

        $controller->listAction($request);

    }
}
