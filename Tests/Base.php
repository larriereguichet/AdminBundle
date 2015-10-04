<?php

namespace BlueBear\AdminBundle\Tests;

use Closure;
use Exception;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Request;

class Base extends WebTestCase
{
    protected static $isDatabaseCreated = false;

    public function initApplication($url = '/', $method = null, $parameters = [])
    {
        // test client initialization
        $client = Client::initClient();
        $this->assertTrue($client != null, 'TestClient successfully initialized');

        if (!self::$isDatabaseCreated) {
            exec(__DIR__ . '/app/console doctrine:database:create --if-not-exists', $output);
            exec(__DIR__ . '/app/console doctrine:schema:update --force', $output);

            foreach ($output as $line) {
                // only in verbose mode
                fwrite(STDOUT, $line . "\n");
            }
            fwrite(STDOUT, "\n");
            self::$isDatabaseCreated = true;
        }
        $request = Request::create($url, $method, $parameters);
        $client->doRequest($request);

        return $client;
    }

    public function assertExceptionRaised($exceptionClass, Closure $closure)
    {
        $e = null;
        $isClassValid = false;

        try {
            $closure();
        } catch (Exception $e) {
            if (get_class($e) == $exceptionClass) {
                $isClassValid = true;
            }
        }
        $this->assertTrue($isClassValid, 'Expected ' . $exceptionClass . ', got ' . get_class($e));
    }
}
