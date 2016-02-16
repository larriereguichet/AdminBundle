<?php

namespace LAG\AdminBundle\Tests;

use AppKernel;
use Symfony\Bundle\FrameworkBundle\Client as BaseClient;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class Client extends BaseClient
{
    protected static $connection;

    protected $requested = true;

    public static function initClient()
    {
        $kernel = new AppKernel('test', true);
        $kernel->loadClassCache();
        $client = new self($kernel);

        return $client;
    }

    /**
     * @see http://alexandre-salome.fr/blog/Symfony2-Isolation-Of-Tests
     *
     * @param Request $request A Request instance
     *
     * @return Response A Response instance
     */
    public function doRequest($request)
    {
        if ($this->requested) {
            $this->getKernel()->shutdown();
            $this->getKernel()->boot();
        }
        if (null === self::$connection) {
            self::$connection = $this->getContainer()->get('doctrine.dbal.default_connection');
        } else {
            $this->getContainer()->set('doctrine.dbal.default_connection', self::$connection);
        }
        $this->requested = true;
        self::$connection->beginTransaction();
        $response = $this->getKernel()->handle($request);
        self::$connection->rollback();

        return $response;
    }
}
