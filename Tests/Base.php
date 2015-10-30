<?php

namespace LAG\AdminBundle\Tests;

use Closure;
use Exception;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\BrowserKit\Cookie;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken;

class Base extends WebTestCase
{
    protected static $isDatabaseCreated = false;

    /**
     * @var ContainerInterface
     */
    protected $container;

    /**
     * @var Client
     */
    protected $client;

    /**
     * Initialize an application with a container and a client. Create database if required.
     *
     * @param string $url
     * @param null   $method
     * @param array  $parameters
     */
    public function initApplication($url = '/', $method = null, $parameters = [])
    {
        // creating kernel client
        $this->client = Client::initClient();
        // test client initialization
        $this->assertTrue($this->client != null, 'TestClient successfully initialized');

        // initialise database
        if (!self::$isDatabaseCreated) {
            // TODO remove database at the end of the tests
            exec(__DIR__.'/app/console doctrine:database:create --if-not-exists', $output);
            exec(__DIR__.'/app/console doctrine:schema:update --force', $output);

            foreach ($output as $line) {
                // only in verbose mode
                fwrite(STDOUT, $line."\n");
            }
            fwrite(STDOUT, "\n");
            self::$isDatabaseCreated = true;
        }
        // init a request
        $request = Request::create($url, $method, $parameters);
        // do request
        $this->client->doRequest($request);
        $this->container = $this->client->getContainer();
    }

    /**
     * Assert that an exception is raised in the given code.
     *
     * @param $exceptionClass
     * @param callable $closure
     */
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
        $this->assertTrue($isClassValid, 'Expected '.$exceptionClass.', got '.get_class($e));
    }

    public function logIn($login = 'admin', $roles = null)
    {
        $session = $this
            ->container
            ->get('session');

        if ($roles === null) {
            $roles = [
                'ROLE_ADMIN',
                'ROLE_USER',
            ];
        }
        $firewall = 'secured_area';
        $token = new UsernamePasswordToken($login, null, $firewall, $roles);
        $session->set('_security_'.$firewall, serialize($token));
        $session->save();

        $cookie = new Cookie($session->getName(), $session->getId());
        $this->client->getCookieJar()->set($cookie);
    }
}
