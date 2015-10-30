<?php

namespace LAG\AdminBundle\Tests;

use LAG\AdminBundle\Controller\GenericController;
use Symfony\Component\HttpFoundation\Request;

class GenericControllerTest extends Base
{
    public function testList()
    {
        $this->initApplication();
        $controller = new GenericController();
        $controller->setContainer($this->container);

        // with no parameters, an exception should be raised
        $request = new Request();
        $this->assertExceptionRaised('Exception', function () use ($controller, $request) {
            $controller->listAction($request);
        });
        // wrong admin and route name should raised an exception
        $request = new Request([], [], [
            '_route_params' => [
                '_admin' => 'bad_admin',
                '_action' => 'bad_route',
            ],
        ]);
        $this->assertExceptionRaised('Exception', function () use ($controller, $request) {
            $controller->listAction($request);
        });
        // right admin
        $this->logIn();
        $request = new Request([], [], [
                '_route_params' => [
                '_admin' => 'test',
                '_action' => 'list',
            ],
            $this->client->getCookieJar(),
            [], [
                'PHP_AUTH_USER' => 'admin',
                'PHP_AUTH_PW' => 'admin',
            ],
        ]);
        //$response = $controller->listAction($request);
    }
}
