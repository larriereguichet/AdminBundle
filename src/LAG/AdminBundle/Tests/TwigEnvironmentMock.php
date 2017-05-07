<?php

namespace LAG\AdminBundle\Tests;

use Twig_Environment;

class TwigEnvironmentMock extends Twig_Environment
{
    public function render($template, array $parameters = [])
    {
        return [
            'template' => $template,
            'parameters' => $parameters,
        ];
    }
}
