<?php

namespace LAG\AdminBundle\Tests;

use Twig_Environment;

class TwigEnvironmentMock extends Twig_Environment
{
    public function render($template, $parameters = [])
    {
        return [
            'template' => $template,
            'parameters' => $parameters,
        ];
    }
}
