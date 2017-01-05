<?php

namespace LAG\AdminBundle\Field;

use Twig_Environment;

interface TwigAwareInterface
{
    /**
     * Define twig environment.
     *
     * @param Twig_Environment $twig
     * @return void
     */
    public function setTwig(Twig_Environment $twig);
}
