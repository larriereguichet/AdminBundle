<?php

namespace LAG\AdminBundle\Tests\Fake;

use Twig_LoaderInterface;

class Twig_Environment extends \Twig_Environment
{
    public function __construct(Twig_LoaderInterface $loader = null, array $options = [])
    {
    }
}
