<?php

use Symfony\Component\Config\Loader\LoaderInterface;
use Symfony\Component\HttpKernel\Kernel;

class AppKernel extends Kernel
{
    public function registerBundles()
    {
        return array(
            new Symfony\Bundle\FrameworkBundle\FrameworkBundle(),
            new Symfony\Bundle\TwigBundle\TwigBundle(),
            new Sensio\Bundle\FrameworkExtraBundle\SensioFrameworkExtraBundle(),
            new Symfony\Bundle\SecurityBundle\SecurityBundle(),
            //new Sensio\Bundle\DistributionBundle\SensioDistributionBundle(),
            new Doctrine\Bundle\DoctrineBundle\DoctrineBundle(),
            new Symfony\Bundle\WebProfilerBundle\WebProfilerBundle(),
            new BlueBear\AdminBundle\BlueBearAdminBundle(),
        );
    }

    public function registerContainerConfiguration(LoaderInterface $loader)
    {
        $loader->load(__DIR__.'/config/config_test.yml');
    }

    /**
     * @return string
     */
    public function getCacheDir()
    {
        $cacheDir = sys_get_temp_dir().'/phpunit/cache';

        if (!is_dir($cacheDir)) {
            mkdir($cacheDir, 0777, true);
        }
        return $cacheDir;
    }

    /**
     * @return string
     */
    public function getLogDir()
    {
        $logDir = sys_get_temp_dir().'/phpunit/logs';

        if (!is_dir($logDir)) {
            mkdir($logDir, 0777, true);
        }
        return $logDir;
    }
}
