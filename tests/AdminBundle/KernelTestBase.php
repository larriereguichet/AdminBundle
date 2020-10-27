<?php

namespace LAG\AdminBundle\Tests;

use BabDev\PagerfantaBundle\BabDevPagerfantaBundle;
use Doctrine\Bundle\DoctrineBundle\DoctrineBundle;
use Knp\Bundle\MenuBundle\KnpMenuBundle;
use LAG\AdminBundle\LAGAdminBundle;
use Nyholm\BundleTest\BaseBundleTestCase;
use Sensio\Bundle\FrameworkExtraBundle\SensioFrameworkExtraBundle;
use Symfony\Bundle\FrameworkBundle\FrameworkBundle;
use Symfony\Bundle\MonologBundle\MonologBundle;
use Symfony\Bundle\SecurityBundle\SecurityBundle;
use Symfony\Bundle\TwigBundle\TwigBundle;
use Symfony\Component\Finder\Finder;
use Symfony\Component\HttpKernel\HttpKernelInterface;
use Symfony\WebpackEncoreBundle\WebpackEncoreBundle;

abstract class KernelTestBase extends BaseBundleTestCase
{
    protected function getBundleClass(): string
    {
        return LAGAdminBundle::class;
    }

    protected function createKernel(): HttpKernelInterface
    {
        $finder = new Finder();
        $finder
            ->in(__DIR__.'/Fixtures/config')
            ->files()
            ->name('*.yaml')
        ;
        $kernel = parent::createKernel();

        foreach ($finder as $fileInfo) {
            $kernel->addConfigFile($fileInfo->getRealPath());
        }
        $kernel->addBundle(FrameworkBundle::class);
        $kernel->addBundle(MonologBundle::class);
        $kernel->addBundle(SensioFrameworkExtraBundle::class);
        $kernel->addBundle(SecurityBundle::class);
        $kernel->addBundle(DoctrineBundle::class);
        $kernel->addBundle(TwigBundle::class);
        $kernel->addBundle(KnpMenuBundle::class);
        $kernel->addBundle(BabDevPagerfantaBundle::class);
        $kernel->addBundle(WebpackEncoreBundle::class);

        $this->bootKernel();

        return $kernel;
    }

    protected function createClient()
    {
        return $this->getContainer()->get('test.client');
    }
}
