<?php

namespace LAG\AdminBundle\Tests;

use Symfony\Component\Finder\Finder;
use Symfony\Component\Yaml\Yaml;

class LAGAdminBundleTest extends KernelTestBase
{
    public function testInitBundle()
    {
        $kernel = $this->createKernel();

        // Get the container
        $container = $kernel->getContainer();

        // Test if declared services exists
        $finder = new Finder();
        $finder
            ->in(__DIR__.'/../../src/Resources/config/services')
            ->files()
        ;
        $services = [];

        foreach ($finder as $file) {
            $data = Yaml::parseFile($file->getRealPath(), Yaml::PARSE_CUSTOM_TAGS);
            $services = array_merge($services, $data['services']);
        }

        foreach ($services as $service => $value) {
            if ('_defaults' === $service) {
                continue;
            }
            $this->assertTrue($container->has($service), sprintf(
                'The service "%s" does not exists in the service container', $service
            ));
        }
    }
}
