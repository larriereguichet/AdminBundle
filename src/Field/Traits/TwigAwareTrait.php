<?php

namespace LAG\AdminBundle\Field\Traits;

use Twig\Environment;

trait TwigAwareTrait
{
    /**
     * @var Environment
     */
    protected $twig;

    /**
     * Defines the Twig engine.
     *
     * @param Environment $twig
     */
    public function setTwig(Environment $twig)
    {
        $this->twig = $twig;
    }
}
