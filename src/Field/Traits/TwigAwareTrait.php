<?php

namespace LAG\AdminBundle\Field\Traits;

use Twig_Environment;

trait TwigAwareTrait
{
    /**
     * @var Twig_Environment
     */
    protected $twig;

    /**
     * Defines the Twig engine.
     *
     * @param Twig_Environment $twig
     */
    public function setTwig(Twig_Environment $twig)
    {
        $this->twig = $twig;
    }
}
