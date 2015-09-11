<?php

namespace BlueBear\AdminBundle\Admin\Render;

use Twig_Environment;

interface TwigRendererInterface extends RendererInterface
{
    public function setTwig(Twig_Environment $twig);
}
