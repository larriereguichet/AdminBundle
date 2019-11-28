<?php

namespace LAG\AdminBundle\Tests;

use Doctrine\Bundle\DoctrineBundle\DoctrineBundle;
use LAG\AdminBundle\LAGAdminBundle;
use Nyholm\BundleTest\BaseBundleTestCase;
use Nyholm\BundleTest\CompilerPass\PublicServicePass;
use Sensio\Bundle\FrameworkExtraBundle\SensioFrameworkExtraBundle;
use Symfony\Bundle\FrameworkBundle\FrameworkBundle;
use Symfony\Bundle\MonologBundle\MonologBundle;
use Symfony\Bundle\SecurityBundle\SecurityBundle;
use Symfony\Bundle\TwigBundle\TwigBundle;
use Symfony\Component\Finder\Finder;
use Symfony\Component\Yaml\Yaml;

class LAGAdminBundleTest extends BaseBundleTestCase
{
    protected function getBundleClass()
    {
        return LAGAdminBundle::class;
    }
    protected function setUp()
    {
        parent::setUp();

        // Make all services public
        $this->addCompilerPass(new PublicServicePass());
    }

    public function testInitBundle()
    {
        $kernel = $this->createKernel();
        $kernel->addConfigFile(__DIR__.'/Fixtures/config/config.yaml');
        $kernel->addBundle(FrameworkBundle::class);
        $kernel->addBundle(MonologBundle::class);
        $kernel->addBundle(SensioFrameworkExtraBundle::class);
        $kernel->addBundle(SecurityBundle::class);
        $kernel->addBundle(DoctrineBundle::class);
        $kernel->addBundle(TwigBundle::class);

        // Boot the kernel.
        $this->bootKernel();

        // Get the container
        $container = $this->getContainer();

        // Test if you services exists
        $finder = new Finder();
        $finder
            ->in(__DIR__.'/../../src/Resources/config/services')
            ->files()
        ;
        $services = [];

        foreach ($finder as $file) {
            $data = Yaml::parseFile($file->getRealPath());
            $services = array_merge($services, $data['services']);
        }

        foreach ($services as $service => $value) {
            if ('_defaults' === $service) {
                continue;
            }
            $this->assertTrue($container->has($service));
        }
    }
}
