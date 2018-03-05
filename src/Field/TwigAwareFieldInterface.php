<?php

namespace LAG\AdminBundle\Field;

use Twig_Environment;

interface TwigAwareFieldInterface
{
    public function setTwig(Twig_Environment $twig);
}
