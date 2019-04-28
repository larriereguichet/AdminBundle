<?php

namespace LAG\AdminBundle\Field;

use Twig\Environment;

interface TwigAwareFieldInterface extends FieldInterface
{
    public function setTwig(Environment $twig);
}
