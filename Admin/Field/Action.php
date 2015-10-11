<?php

namespace BlueBear\AdminBundle\Admin\Field;

use BlueBear\AdminBundle\Admin\Field;
use Twig_Environment;

class Action extends Link
{
    public function render($value)
    {
        $value = $this->title;

        return parent::render($value);
    }

    public function getType()
    {
        return 'action';
    }

    /**
     * Define Twig engine
     *
     * @param Twig_Environment $twig
     */
    public function setTwig(Twig_Environment $twig)
    {
        $this->twig = $twig;
    }
}
