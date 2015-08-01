<?php

namespace BlueBear\AdminBundle\Tests;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Request;

class Base extends WebTestCase
{
    protected static $isDatabaseCreated = false;

    public function initApplication($url = '/')
    {
        // test client initialization
        $client = Client::initClient();
        $this->assertTrue($client != null, 'TestClient successfully initialized');

        if (!self::$isDatabaseCreated) {
            exec(__DIR__ . '/app/console doctrine:database:create', $output);
            exec(__DIR__ . '/app/console doctrine:schema:update', $output);
            var_dump($output);
            self::$isDatabaseCreated = true;
        }
        $request = Request::create($url);
        $client->doRequest($request);

        return $client;
    }
}
